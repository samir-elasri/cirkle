<?php
namespace App\Imports;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubscriptionPrice;
use App\Models\Core\Subscription;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Import du formulaire MASTER 2350 (« 1 WEB MASTER 2350 COLONNE ABCD », format 010626).
 *
 * Anatomie du fichier (une fiche par classeur, ~200 fiches à venir) :
 *   col A = libellés internes / marqueurs de section (jamais affichés)
 *   col B = « O » : la ligne est un service cochable par le fournisseur
 *   col C = le texte affiché (espaces et couleurs LITTÉRAUX; le rouge disparaît à l'import,
 *           le bleu et les autres couleurs persistent — spec transcript + xlsx)
 *   col D = « X » : le fournisseur saisit un texte personnalisé pour cette ligne
 *
 * Lit le fichier avec PhpSpreadsheet directement (et non Maatwebsite ToCollection)
 * parce que la mise en forme riche (runs de couleur dans les cellules) doit être capturée.
 */
class ExcelImport
{
    private const RED = 'FF0000';

    /** @var array<string> Avertissements non bloquants accumulés pendant l'import */
    public array $warnings = [];

    public function import(string $path, ?string $originalName = null): array
    {
        // Lecture sur une COPIE temporaire : la détection de format (ZipArchive)
        // peut réécrire/normaliser silencieusement le fichier source (+quelques octets).
        $tmp = tempnam(sys_get_temp_dir(), 'master2350_');
        if ($tmp === false || !copy($path, $tmp)) {
            throw new \RuntimeException("Impossible de copier le fichier d'import : {$path}");
        }

        try {
            $worksheet = IOFactory::createReaderForFile($tmp)->load($tmp)->getSheet(0);
            // Un seul importeur, deux formats (auto-détection) :
            //  - « master_fr »   : MASTER 2350 FR, libellés de section en colonne A.
            //  - « english_2col »: format anglais de Denis (25.06) — col A = « O », texte en col B.
            if ($this->detectFormat($worksheet) === 'english_2col') {
                return $this->importEnglish2col($worksheet, $originalName ?? basename($path));
            }
            return $this->importFromCopy($worksheet);
        } finally {
            @unlink($tmp);
        }
    }

    private function importFromCopy(Worksheet $worksheet): array
    {
        $highestRow = $worksheet->getHighestDataRow();

        // ── Métadonnées de la fiche ──
        $clientele = $langue = $titreInterne = $categorie = $profession = null;

        $services = [];      // lignes « O » de SECTION SERVICES OFFERTS
        $capabilities = [];  // lignes « O » de SECTION PERSONALISE CAPABILITIES
        $customersText = []; // SECTION CUSTOMERS TEXT (formulaire standard)
        $capabilitiesText = [];
        $feeText = [];       // bloc OBLIGATOIRE … FRAIS POUR LA FICHE … (porte d'acceptation)
        $forfaitPrices = [];   // forfaits d'abonnement : [ ['state'=>?int, 'duration'=>int, 'cost'=>int] ]
        $websiteForfaits = []; // forfait « site web du fournisseur » (tranche #4) : [ ['tier','duration','cost'] ]
        $keywords = [];

        $priceState = null;    // cible du forfait courant : NULL = code postal, sinon id de State (province)
        $priceLang = 'fr';     // langue de la sous-section forfait (bascule à « ENGLISH VERSION »/CENSUS)
        $websiteTier = null;   // palier site web courant (100/150)
        $provinceCache = [];   // label province => id State (mémoïsation)

        $state = 'preamble';
        $blankStreak = 0;
        $blankSinceLastO = false; // une ligne vide vue depuis la dernière ligne « O » (espacement littéral)

        for ($row = 1; $row <= $highestRow; $row++) {
            $a = $this->plainText($worksheet->getCell("A{$row}"));
            $b = trim($this->plainText($worksheet->getCell("B{$row}")));
            $cCell = $worksheet->getCell("C{$row}");
            $c = $this->plainText($cCell); // brut : espaces préservés
            $d = trim($this->plainText($worksheet->getCell("D{$row}")));

            $aFlat = $this->flatten($a);

            // Ligne entièrement vide : marque un saut de bloc dans le fichier
            if (trim($a) === '' && $b === '' && trim($c) === '' && $d === '') {
                $blankSinceLastO = true;
            }

            // Notes au programmeur : jamais importées
            if (str_contains($aFlat, 'PROGRAMM')) {
                continue;
            }

            // ── Transitions de section (marqueurs colonne A, + FORFAITS via colonne C) ──
            if (str_contains($aFlat, 'CLIENT') && str_contains($aFlat, 'CIBLE')) {
                $clientele = trim($c);
                continue;
            }
            if (trim($aFlat) === 'LANGUE') {
                $langue = strtolower(substr(trim($c), 0, 2));
                continue;
            }
            if (str_contains($aFlat, 'TITRE INTERNE')) {
                $titreInterne = trim($c);
                continue;
            }
            if (str_contains($aFlat, 'GORIE')) { // CATÉGORIE / CATEGORIE
                $categorie = trim($c);
                continue;
            }
            if (str_contains($aFlat, 'PROFESSION')) {
                $profession = trim($c);
                continue;
            }
            if (str_contains($aFlat, 'SECTION SERVICES')) {
                $state = 'services';
                $blankSinceLastO = false; // pas d'espace avant la première ligne d'une section
                // la ligne du marqueur porte déjà un service (B=O) : ne pas sauter
            } elseif (str_contains($aFlat, 'TEXT') && preg_match('/C[OU]STU?OMERS|CONSTOMERS/', $aFlat)) {
                $state = 'customers';
            } elseif (str_contains($aFlat, 'CAPABILITIES TEXT')) {
                $state = 'capabilities_text';
            } elseif (str_contains($aFlat, 'CAPABILITIES')) {
                $state = 'capabilities';
                $blankSinceLastO = false;
            } elseif (str_contains($aFlat, 'KEYMORD') || str_contains($aFlat, 'KEYWORD')) {
                $state = 'keywords';
                $blankStreak = 0;
                // la ligne du marqueur porte déjà le premier mot-clé en C
            } elseif (str_contains($this->flatten($c), 'FORFAIT') && str_contains($aFlat, 'SECTION')) {
                $state = 'prices';
                continue;
            } elseif (str_contains($this->flatten($c), 'TARIF PRICING')) {
                // Fichiers anglais au format 3 colonnes (master EN 25.06) : la zone forfaits
                // commence à « TARIF PRICING … » SANS marqueur de section en colonne A.
                // Pas de « continue » : la ligne d'en-tête règle aussi priceLang/priceState.
                $state = 'prices';
            } elseif (in_array($state, ['prices', 'website', 'skip'], true)
                && str_contains($this->flatten($c), 'CONCLUSION')) {
                // Ligne « OBLIGATOIRE/MANDATORY … page CONCLUSION » au bas de la fiche :
                // tout ce qui suit (après le séparateur « …… ») est la liste des mots-clés
                // SEO — même quand le marqueur KEYWORD de la colonne A manque ou est décalé.
                $state = 'keywords';
                $blankStreak = 0;
                continue;
            } elseif (str_starts_with($this->flatten($c), 'OBLIGATOIRE')) {
                // Bloc « FRAIS POUR LA FICHE DE COMPETENCE » : c'est la porte d'acceptation
                // des frais (feature #6). On capture son texte; les lignes « O » de ce bloc
                // ne sont PAS des services.
                $state = 'fee';
            }

            // ── Collecte selon la section courante ──
            switch ($state) {
                case 'services':
                case 'capabilities':
                    if ($b === 'O') {
                        // Les lignes « OPTION : … » (les 6 options payantes : permis, diplômes,
                        // photos, promotion, estimation, recrutement) ne sont PAS des services —
                        // l'app les gère séparément. On ne les importe pas comme services.
                        if (preg_match('/^\s*OPTION\b/iu', $c)) {
                            break;
                        }
                        $html = $this->formattedText($cCell);
                        if (trim($c) === '' || $html === '') {
                            break; // entièrement rouge ou vide : disparaît
                        }
                        // Champ de saisie (format 18.06.26) : « PRÉCISEZ » en gris pâle dans la
                        // colonne C marque le champ. On lit UNIQUEMENT les colonnes A, B, C — les
                        // colonnes D et + sont les notes personnelles de Denis (à ignorer, 22.06).
                        // On retire le mot-marqueur du libellé — le champ « Précisez » le remplace.
                        // « SPECIFY » = équivalent anglais (le master EN 25.06 est au format
                        // 3 colonnes avec des libellés SPECIFY — même règle que PRÉCISEZ).
                        $hasInput = (bool) preg_match('/PR[ÉE]CISEZ|PAR\s+FOURNISSEUR|SPECIFY/iu', $c);
                        if ($hasInput) {
                            $c = trim(preg_replace('/\s*:?\s*(PR[ÉE]CISEZ|PAR\s+FOURNISSEUR|SPECIFY)\s*:?\s*$/iu', '', $c));
                            $html = $this->stripInputMarker($html);
                        }
                        $entry = [
                            'title' => $c,
                            'formatted_title' => $html,
                            'has_input' => $hasInput,
                            'source_row' => $row,
                            'gap_before' => $blankSinceLastO,
                        ];
                        $blankSinceLastO = false;
                        if ($state === 'services') {
                            $services[] = $entry;
                        } else {
                            $capabilities[] = $entry;
                        }
                    } elseif ($state === 'capabilities' && trim($c) !== '' && !str_starts_with(trim($c), '…')) {
                        // texte descriptif de la section capabilities (listes d'édifices, etc.)
                        $capabilitiesText[] = $c;
                    }
                    break;

                case 'customers':
                case 'capabilities_text':
                    if (trim($c) !== '' && !str_starts_with(trim($c), '…')) {
                        if ($state === 'customers') {
                            $customersText[] = $c;
                        } else {
                            $capabilitiesText[] = $c;
                        }
                    }
                    break;

                case 'fee':
                    // Le bloc frais se termine au séparateur « …… » : tout ce qui suit
                    // (COÛT DES SERVICES, « PAR LE FOURNISSEUR » répétés, suggestions…)
                    // n'appartient PAS aux frais. On quitte l'état pour ne plus capturer.
                    if (str_starts_with(trim($c), '…')) {
                        $state = 'preamble';
                        break;
                    }
                    if (trim($c) !== '') {
                        $feeText[] = trim($c);
                    }
                    break;

                case 'prices':
                    // Forfaits d'abonnement : code postal (state NULL) + une section par PROVINCE.
                    // Le fichier contient le bloc FR puis le bloc EN (« ENGLISH VERSION ») : on ne
                    // garde que les prix de la langue de la fiche (les montants 3 mois diffèrent).
                    $cFlat = $this->flatten($c);

                    // Frontière : la section SITE WEB (forfait distinct) commence à un marqueur SECTION.
                    if (str_contains($aFlat, 'SECTION') || str_contains($cFlat, 'SITE WEB') || str_contains($cFlat, 'SUPPLIER WEB')) {
                        $state = 'website';
                        break;
                    }

                    // Langue de la sous-section courante
                    if (str_contains($cFlat, 'ENGLISH VERSION') || str_contains($cFlat, 'TARIF PRICING') || str_contains($cFlat, 'CENSUS')) {
                        $priceLang = 'en';
                    } elseif (str_contains($cFlat, 'PRIX FORFAIT') || str_contains($cFlat, 'RECENSEMENT')) {
                        $priceLang = 'fr';
                    }

                    // En-tête de cible : code postal vs province (le nom de la BC est sur sa propre ligne)
                    if (str_contains($cFlat, 'PRIX FORFAIT') || str_contains($cFlat, 'TARIF PRICING')) {
                        $priceState = null;
                    } elseif ($label = $this->provinceLabel($cFlat)) {
                        $priceState = $this->provinceStateId($label, $provinceCache);
                    }

                    // Lignes « GAIN de/of … » : économies affichées, pas des prix (elles
                    // commencent aussi par « 3 MOIS/MONTHS … » et le « O » de Denis est
                    // parfois décalé dessus — fichiers WW0002).
                    if (str_contains($cFlat, 'GAIN') || str_contains($cFlat, 'RABAIS')) {
                        break;
                    }

                    // Ligne de prix « O » : on ne retient que la langue de la fiche.
                    // (Le « O » n'est pas exigé si la durée ouvre la ligne — O parfois décalé.)
                    if (($b === 'O' || preg_match('/^\d+\s*(MOIS|MONTH)/iu', trim($c)))
                        && $priceLang === ($langue ?: 'fr')
                        && preg_match('/(\d+)\s*(MOIS|MONTH)/iu', $c, $m)) {
                        $duration = (int)$m[1];
                        $cost = $this->lastAmount($c, $duration);
                        if ($cost !== null) {
                            $forfaitPrices[] = ['state' => $priceState, 'duration' => $duration, 'cost' => $cost];
                        }
                    }
                    break;

                case 'website':
                    // Forfait « site web du fournisseur » : 2 paliers (100$/150$), 1/6/12 mois.
                    // Capturé ici pour la tranche #4; pas encore persisté.
                    $cFlat = $this->flatten($c);

                    // Fin des forfaits → début des OPTIONS payantes (permis/diplômes/photos/promotion)
                    if (str_contains($aFlat, 'OPTION') || str_contains($cFlat, 'AJOUTER LES OPTIO')) {
                        $state = 'skip';
                        break;
                    }
                    if (str_contains($cFlat, 'ENGLISH VERSION') || str_contains($cFlat, 'SUPPLIER WEB')) {
                        $priceLang = 'en';
                    } elseif (str_contains($cFlat, 'SITE WEB DU FOURN') || str_contains($cFlat, 'FRAIS SITE WEB')) {
                        $priceLang = 'fr';
                        if (preg_match('/(\d{2,4})\s*\$/u', $c, $mt)) {
                            $websiteTier = (int) $mt[1]; // palier 100 / 150
                        }
                    }
                    if (str_contains($cFlat, 'GAIN') || str_contains($cFlat, 'RABAIS')) {
                        break; // économies affichées, pas des prix
                    }
                    if ($b === 'O' && $priceLang === ($langue ?: 'fr') && $websiteTier
                        && preg_match('/(\d+)\s*(MOIS|MONTH)/iu', $c, $m)) {
                        $duration = (int)$m[1];
                        $cost = $this->lastAmount($c, $duration);
                        if ($cost !== null) {
                            $websiteForfaits[] = ['tier' => $websiteTier, 'duration' => $duration, 'cost' => $cost];
                        }
                    }
                    break;

                case 'keywords':
                    if (trim($c) !== '') {
                        // séparateurs « …… » : pas des mots-clés
                        if (!preg_match('/^[….]{3,}/u', trim($c))) {
                            $keywords[] = trim($c);
                        }
                        $blankStreak = 0;
                    } elseif (++$blankStreak >= 5) {
                        $state = 'done';
                    }
                    break;
            }

            if ($state === 'done') {
                break;
            }
        }

        // ── Validation minimale ──
        if (!$titreInterne || !$categorie || !$profession) {
            throw new \RuntimeException(
                'Fiche invalide : TITRE INTERNE, CATÉGORIE ou PROFESSION introuvable (format MASTER 2350 attendu).'
            );
        }
        if (empty($services)) {
            throw new \RuntimeException('Fiche invalide : aucune ligne de service « O » trouvée.');
        }

        // Garde-fou : W#### à UN seul W = convention de Denis pour ses MASTERS (outils de
        // travail hors site, jamais vus par les fournisseurs); les fiches réelles = WW####.
        // Importer un master écraserait/mélangerait la fiche du même label — on avertit.
        if (preg_match('/^W\d{3,4}/i', $titreInterne) && stripos($titreInterne, 'WW') !== 0) {
            $this->warnings[] = "TITRE INTERNE « {$titreInterne} » ressemble à un MASTER (W#### à un seul W). "
                . 'Les fiches réelles utilisent WW#### — vérifiez le fichier avant de publier.';
        }

        app()->setLocale(in_array($langue, getLocales(), true) ? $langue : 'fr');

        // ── Persistance ──
        $parentModel = ServiceCategory::firstOrCreate(['label' => $categorie]);
        if (empty($parentModel->title)) {
            $parentModel->title = $categorie;
            $parentModel->save();
        }

        $categoryModel = ServiceCategory::firstOrCreate(['label' => $titreInterne]);
        $categoryModel->service_category_id = $parentModel->id;
        $categoryModel->title = $profession;
        $categoryModel->provider_type = $this->providerType($clientele);
        // Frais de la fiche : valeur du TITRE INTERNE si présente, sinon flat par plateforme
        // (Denis 24.06 : résidentiel 75 $, B2B 100 $).
        $categoryModel->fiche_fee = $this->ficheFee($titreInterne)
            ?? ($categoryModel->provider_type === 'business' ? 100 : 75);
        $categoryModel->fiche_fee_text = $feeText ? implode('<br>', $feeText) : null;
        // Palier site web PROPRE À LA FICHE (Denis 03.07 : « mes fiches déterminent le
        // 100 $ ou le 150 $ ») : premier palier de la section SITE WEB du fichier.
        $categoryModel->website_tier = $websiteForfaits[0]['tier'] ?? null;
        $categoryModel->customers_text = implode('<br>', $customersText);
        $categoryModel->capabilities_text = implode('<br>', $capabilitiesText);
        $categoryModel->keywords_json = json_encode($keywords, JSON_UNESCAPED_UNICODE);
        $categoryModel->save();

        // Ré-import idempotent : purge les anciens services de la fiche non liés à un fournisseur
        Service::where('service_category_id', $categoryModel->id)
            ->whereNotIn('id', \App\Models\SubscriberService::pluck('service_id')->filter())
            ->delete();

        foreach (['service' => $services, 'capability' => $capabilities] as $type => $entries) {
            foreach ($entries as $entry) {
                Service::create([
                    'service_category_id' => $categoryModel->id,
                    'type' => $type,
                ] + $entry);
            }
        }

        [$postalPrices, $provinceCount] = $this->persistForfaitPrices($forfaitPrices, $categoryModel);

        return [
            'profession' => $profession,
            'provider_type' => $categoryModel->provider_type,
            'fiche_fee' => $categoryModel->fiche_fee,
            'locale' => app()->getLocale(),
            'services' => count($services),
            'capabilities' => count($capabilities),
            'prices' => $postalPrices,
            'province_prices' => $provinceCount,
            'website_forfaits' => count($websiteForfaits),
            'keywords' => count($keywords),
            'warnings' => $this->warnings,
        ];
    }

    /**
     * Persiste les forfaits d'abonnement (code postal + provinces) d'une fiche.
     * Retourne [postalPrices (durée => coût), provinceCount].
     */
    private function persistForfaitPrices(array $forfaitPrices, ServiceCategory $categoryModel): array
    {
        $postalPrices = [];
        $provinceCount = 0;
        foreach ($forfaitPrices as $fp) {
            $subscription = Subscription::where('duration', '=', $fp['duration'])->where('active', '=', true)->first();
            if (!$subscription) {
                $this->warnings[] = "Aucune Subscription active de {$fp['duration']} mois : prix {$fp['cost']}\$ ignoré.";
                continue;
            }
            $price = SubscriptionPrice::firstOrCreate([
                'service_category_id' => $categoryModel->id,
                'subscription_id' => $subscription->id,
                'state_id' => $fp['state'], // NULL = code postal
            ]);
            $price->cost = $fp['cost'];
            $price->save();

            if ($fp['state'] === null) {
                $postalPrices[$fp['duration']] = $fp['cost'];
            } else {
                $provinceCount++;
            }
        }

        return [$postalPrices, $provinceCount];
    }

    /**
     * Détecte le format du classeur :
     *   - « master_fr »    : libellés de section dans la colonne A (TITRE INTERNE, SECTION…).
     *   - « english_2col » : colonne A uniquement « O »/vide, tout le texte en colonne B.
     * Lève une exception si le format n'est reconnu ni l'un ni l'autre (jamais d'import muet).
     */
    private function detectFormat(Worksheet $worksheet): string
    {
        $hi = min(80, $worksheet->getHighestDataRow());
        $colAhasLabels = false;
        $colAonlyO = true;

        for ($r = 1; $r <= $hi; $r++) {
            $a = trim($this->flatten($this->plainText($worksheet->getCell("A{$r}"))));
            if ($a === '' || $a === 'O') {
                continue;
            }
            $colAonlyO = false;
            if (preg_match('/TITRE INTERNE|SECTION|GORIE|PROFESSION|CIBLE|LANGUE/', $a)) {
                $colAhasLabels = true;
                break;
            }
        }

        if ($colAhasLabels) {
            return 'master_fr';
        }
        if ($colAonlyO) {
            return 'english_2col';
        }

        throw new \RuntimeException('Format de fiche non reconnu (ni MASTER 2350 FR, ni format anglais 2 colonnes).');
    }

    /**
     * Importeur du format ANGLAIS « 2 colonnes » de Denis (25.06) :
     *   col A = « O » (service cochable)   col B = le texte affiché
     * AUCUN libellé de section en colonne A. Métadonnées positionnelles (code interne,
     * plateforme, langue, catégorie, profession) dans les 1res lignes de la colonne B.
     * Le NOM DE FICHIER (WW0001RE / WW0001B2BE) est la source FIABLE du code + de la
     * plateforme — le contenu est parfois incohérent (« B2BRESIDENTIAL », « W000RE »…).
     *
     * Limites v1 (signalées dans les warnings) : tous les « O » hors OPTIONS et lignes de
     * prix sont importés comme SERVICES (compétences fusionnées); les forfaits/province
     * (non encore utilisés) et les mots-clés ne sont pas importés.
     */
    private function importEnglish2col(Worksheet $worksheet, ?string $originalName): array
    {
        $highestRow = $worksheet->getHighestDataRow();

        // ── Ligne du code interne (col B commence par « W#### », « WW#### » ou
        //    « WS#### » — Denis a changé de préfixe le 03.07) ──
        $titleRow = null;
        for ($r = 1; $r <= min(40, $highestRow); $r++) {
            if (preg_match('/^W[WS]?\d{3,4}/i', trim($this->plainText($worksheet->getCell("B{$r}"))))) {
                $titleRow = $r;
                break;
            }
        }
        if ($titleRow === null) {
            throw new \RuntimeException('Fiche anglaise invalide : code interne (W####) introuvable en colonne B.');
        }

        // ── Métadonnées : 5 premières valeurs non vides après le code ──
        // ordre observé sur les 4 fichiers : [plateforme, langue, catégorie, profession, sous-titre]
        $meta = [];
        for ($r = $titleRow + 1; $r <= $highestRow && count($meta) < 5; $r++) {
            $b = trim($this->plainText($worksheet->getCell("B{$r}")));
            if ($b === '' || preg_match('/^[….]{3,}/u', $b)) {
                continue;
            }
            $meta[] = $b;
        }
        $platformStr = $meta[0] ?? '';
        $langStr     = $meta[1] ?? '';
        $categorie   = $meta[2] ?? null;
        $profession  = $meta[3] ?? null;

        $langue = (stripos($langStr, 'FREN') !== false || stripos($langStr, 'FRAN') !== false) ? 'fr' : 'en';

        // ── Code interne + plateforme : le NOM DE FICHIER prime (contenu peu fiable) ──
        $code = strtoupper(trim(explode(' ', trim((string) pathinfo($originalName ?? '', PATHINFO_FILENAME)))[0] ?? ''));
        $providerType = null;
        if ($code !== '') {
            if (str_contains($code, 'B2B')) {
                $providerType = 'business';
            } elseif (preg_match('/RE$/', $code) || str_contains($code, 'RESID')) {
                $providerType = 'residential';
            }
        }
        $providerType = $providerType ?? $this->providerType($platformStr) ?? 'residential';
        $titreInterne = $code !== '' ? $code : trim($this->plainText($worksheet->getCell("B{$titleRow}")));

        if (!$titreInterne || !$categorie || !$profession) {
            throw new \RuntimeException('Fiche anglaise invalide : code, catégorie ou profession manquant.');
        }

        // ── Services : tous les « O » (hors OPTIONS / prix) jusqu'à la section TARIF ──
        $services = [];
        $priceZoneStart = null; // 1re ligne de la zone forfaits (TARIF/CENSUS)
        for ($r = $titleRow + 1; $r <= $highestRow; $r++) {
            $b = $this->plainText($worksheet->getCell("B{$r}"));
            $bt = trim($b);
            $bFlat = $this->flatten($bt);

            // Frontière : la zone services/compétences se termine au début des forfaits/tarifs.
            if (str_contains($bFlat, 'TARIF') || str_contains($bFlat, 'CENSUS') || str_contains($bFlat, 'RECENSEMENT')) {
                $priceZoneStart = $r;
                break;
            }
            if (trim($this->plainText($worksheet->getCell("A{$r}"))) !== 'O') {
                continue;
            }
            if ($bt === '' || preg_match('/^OPTION\b/i', $bt)) {
                continue; // ligne vide, ou OPTION payante (gérée séparément par l'app)
            }
            if (preg_match('/^\s*\d[\d.,\s]*\$\s*$/u', $bt) || preg_match('/\d+\s*(MONTH|MOIS)/i', $bt)) {
                continue; // ligne de prix (forfait)
            }

            // « SPECIFY: » — et Denis glisse parfois le français « PRÉCISEZ » dans ses
            // fiches anglaises (WS0001RE 03.07) : les deux ouvrent le champ de saisie.
            $hasInput = (bool) preg_match('/SPECIFY\s*:|PR[ÉE]CISEZ/iu', $bt);
            $title = $bt;
            $html = $this->formattedText($worksheet->getCell("B{$r}"));
            if ($hasInput) {
                $title = trim(preg_replace('/\s*:?\s*(SPECIFY|PR[ÉE]CISEZ)\s*:?\s*$/iu', '', $bt));
                $html = trim(preg_replace('/(SPECIFY|PR[ÉE]CISEZ)\s*:?/iu', '', $html));
            }
            if ($title === '') {
                continue;
            }
            $services[] = [
                'title' => $title,
                'formatted_title' => $html !== '' ? $html : e($title),
                'has_input' => $hasInput,
                'source_row' => $r,
                'gap_before' => false,
            ];
        }

        if (empty($services)) {
            throw new \RuntimeException('Fiche anglaise invalide : aucun service « O » trouvé.');
        }

        // ── Forfaits + mots-clés SEO (zone après les services) ──
        // Layout observé (WW0001RE/B2BE, WW0002RE/B2BE) :
        //   « TARIF PRICING  POSTAL CODE » → 4 lignes « O » (montants nus 75$/216$/…
        //   OU « 1 MONTH 100 $ » explicites — les deux existent selon le fichier);
        //   puis un bloc par province (« ONTARIO CENSUS … » → « 1 MONTH 5,000 $ »);
        //   puis « TARIF SUPPLIER WEB SITE » (forfait site web — config, non persisté);
        //   puis les lignes OPTION;
        //   puis « MANDATORY … <<CONCLUSION>> » et, après le séparateur « …… »,
        //   la liste des mots-clés SEO jusqu'à la fin du fichier.
        $forfaitPrices = [];
        $keywords = [];
        $websiteTier = null;       // palier site web de LA fiche (prix « 1 MONTH » de la section WEB SITE)
        if ($priceZoneStart !== null) {
            $zone = null;          // 'postal' | 'province' | 'website' | 'keywords'
            $priceState = null;    // NULL = code postal, sinon id State (province)
            $provinceCache = [];
            $postalOrder = [1, 3, 6, 12];
            $postalIdx = 0;        // durée implicite des montants nus, dans l'ordre du fichier

            for ($r = $priceZoneStart; $r <= $highestRow; $r++) {
                $a = trim($this->plainText($worksheet->getCell("A{$r}")));
                $bt = trim($this->plainText($worksheet->getCell("B{$r}")));
                if ($bt === '') {
                    continue;
                }
                $bFlat = $this->flatten($bt);

                if ($zone === 'keywords') {
                    if (!preg_match('/^[….]{3,}/u', $bt)) {
                        $keywords[] = $bt;
                    }
                    continue;
                }
                if (str_contains($bFlat, 'CONCLUSION')) {
                    $zone = 'keywords';
                    continue;
                }

                // En-têtes de zone
                if (str_contains($bFlat, 'POSTAL CODE')) {
                    $zone = 'postal';
                    $priceState = null;
                    $postalIdx = 0;
                    continue;
                }
                if ($label = $this->provinceLabel($bFlat)) {
                    $zone = 'province';
                    $priceState = $this->provinceStateId($label, $provinceCache);
                    continue;
                }
                if (str_contains($bFlat, 'WEB SITE') || str_contains($bFlat, 'WEBSITE')) {
                    $zone = 'website'; // forfait site web : géré par config, pas par fiche
                    continue;
                }
                if (preg_match('/^OPTION\b/i', $bt)) {
                    $zone = null;
                    continue;
                }

                // Lignes « GAIN of … » : économies affichées, pas des prix. À exclure AVANT
                // le motif durée (elles commencent aussi par « 3 MONTHS … ») — dans les
                // fichiers WW0002 le « O » est décalé d'une ligne et tombe sur la ligne GAIN.
                if (str_contains($bFlat, 'GAIN')) {
                    continue;
                }
                // Section SITE WEB : on ne persiste pas ses prix (config), mais le prix
                // « 1 MONTH » EST le palier de la fiche (100 $ ou 150 $ — Denis 03.07).
                if ($zone === 'website') {
                    if ($websiteTier === null && preg_match('/^1\s*(MONTH|MOIS)\b/i', $bt)) {
                        $websiteTier = $this->lastAmount($bt, 1);
                    }
                    continue;
                }
                if ($zone === null) {
                    continue;
                }

                // Ligne de prix : durée explicite « N MONTH(S) » en début de ligne (le « O »
                // n'est PAS exigé — Denis le décale parfois d'une ligne), ou montant nu AVEC
                // « O » dans la section code postal (durées 1/3/6/12 implicites, dans l'ordre).
                if (preg_match('/^(\d+)\s*(MONTH|MOIS)/i', $bt, $m)) {
                    $duration = (int) $m[1];
                } elseif ($a === 'O' && $zone === 'postal' && $postalIdx < 4
                    && preg_match('/^[\d][\d\s,.]*\$$/u', preg_replace('/\s+/', ' ', $bt))) {
                    $duration = $postalOrder[$postalIdx];
                } else {
                    continue;
                }

                $cost = $this->lastAmount($bt, $duration);
                if ($cost !== null) {
                    $forfaitPrices[] = [
                        'state' => $zone === 'province' ? $priceState : null,
                        'duration' => $duration,
                        'cost' => $cost,
                    ];
                    if ($zone === 'postal') {
                        $postalIdx++;
                    }
                }
            }
        }

        app()->setLocale(in_array($langue, getLocales(), true) ? $langue : 'en');

        // ── Persistance (mêmes règles que le format FR; frais = forfait plat par plateforme) ──
        $parentModel = ServiceCategory::firstOrCreate(['label' => $categorie]);
        if (empty($parentModel->title)) {
            $parentModel->title = $categorie;
            $parentModel->save();
        }

        $categoryModel = ServiceCategory::firstOrCreate(['label' => $titreInterne]);
        $categoryModel->service_category_id = $parentModel->id;
        $categoryModel->title = $profession;
        $categoryModel->provider_type = $providerType;
        $categoryModel->fiche_fee = \App\Support\FicheFee::for($providerType);
        $categoryModel->fiche_fee_text = null;
        $categoryModel->website_tier = $websiteTier; // palier site web de la fiche (Denis 03.07)
        $categoryModel->customers_text = '';
        $categoryModel->capabilities_text = '';
        $categoryModel->keywords_json = json_encode($keywords, JSON_UNESCAPED_UNICODE);
        $categoryModel->save();

        // Ré-import idempotent : purge les anciens services de la fiche non liés à un fournisseur
        Service::where('service_category_id', $categoryModel->id)
            ->whereNotIn('id', \App\Models\SubscriberService::pluck('service_id')->filter())
            ->delete();

        foreach ($services as $entry) {
            Service::create(['service_category_id' => $categoryModel->id, 'type' => 'service'] + $entry);
        }

        [$postalPrices, $provinceCount] = $this->persistForfaitPrices($forfaitPrices, $categoryModel);
        if (empty($postalPrices)) {
            $this->warnings[] = 'Aucun forfait CODE POSTAL trouvé : l\'inscription fournisseur échouera pour cette fiche (« forfait non disponible »).';
        }

        return [
            'profession' => $profession,
            'provider_type' => $categoryModel->provider_type,
            'fiche_fee' => $categoryModel->fiche_fee,
            'locale' => app()->getLocale(),
            'services' => count($services),
            'capabilities' => 0,
            'prices' => $postalPrices,
            'province_prices' => $provinceCount,
            'website_forfaits' => 0,
            'keywords' => count($keywords),
            'titre_interne' => $titreInterne,
            'format' => 'english_2col',
            'warnings' => array_merge($this->warnings, [
                'Format anglais 2 colonnes : compétences fusionnées avec les services (v1).',
            ]),
        ];
    }

    /**
     * Frais de la fiche, lus dans le TITRE INTERNE : « 0001 RF 75/100/100 » → 75.
     * Premier nombre situé APRÈS le code alpha (RF/B2B/…), pour ne pas prendre l'identifiant.
     */
    private function ficheFee(string $titreInterne): ?float
    {
        if (preg_match('/[A-Za-zÀ-ÿ]{1,5}\s+(\d+(?:[.,]\d+)?)/u', $titreInterne, $m)) {
            return (float)str_replace(',', '.', $m[1]);
        }

        return null;
    }

    /**
     * Texte brut d'une cellule, espaces préservés (aucun trim).
     */
    private function plainText(Cell $cell): string
    {
        $value = $cell->getValue();

        if ($value instanceof RichText) {
            return $value->getPlainText();
        }

        return (string)$value;
    }

    /**
     * Retire le marqueur « PRÉCISEZ »/« PAR FOURNISSEUR » (et son « : ») du libellé HTML
     * formaté : à l'écran, le champ de saisie « Précisez » remplace ce mot. Nettoie aussi
     * les espaces et les <span> vides résiduels pour ne pas laisser de trou.
     */
    private function stripInputMarker(string $html): string
    {
        $html = preg_replace('/(PR[ÉE]CISEZ|PAR\s+FOURNISSEUR|SPECIFY)\s*:?/iu', '', $html);
        $html = preg_replace('/<span[^>]*>\s*<\/span>/iu', '', $html);
        return trim($html);
    }

    /**
     * Texte de la colonne C avec sa mise en forme littérale, en HTML sûr :
     * couleurs par fragment, gras/italique/souligné, espaces intacts.
     * Les fragments ROUGES sont supprimés (ils « disparaissent » à l'import — spec).
     */
    private function formattedText(Cell $cell): string
    {
        $value = $cell->getValue();

        if ($value instanceof RichText) {
            $html = '';
            foreach ($value->getRichTextElements() as $element) {
                $font = $element->getFont(); // null pour un TextElement sans mise en forme
                $rgb = $font && $font->getColor() ? strtoupper($font->getColor()->getRGB()) : null;

                if ($rgb === self::RED) {
                    continue;
                }

                $html .= $this->wrap(e($element->getText()), $font, $rgb);
            }
            return $html;
        }

        $text = (string)$value;
        if (trim($text) === '') {
            return '';
        }

        $font = $cell->getStyle()->getFont();
        $rgb = strtoupper($font->getColor()->getRGB());

        if ($rgb === self::RED) {
            return '';
        }

        return $this->wrap(e($text), $font, $rgb);
    }

    /**
     * Enrobe un fragment échappé avec sa mise en forme (couleur non noire, gras, italique, souligné).
     */
    private function wrap(string $escaped, $font, ?string $rgb): string
    {
        if (!$font) {
            return $escaped;
        }

        $open = '';
        $close = '';

        if ($rgb && $rgb !== '000000') {
            $open .= '<span style="color:#' . $rgb . '">';
            $close = '</span>' . $close;
        }
        if ($font->getBold()) {
            $open .= '<strong>';
            $close = '</strong>' . $close;
        }
        if ($font->getItalic()) {
            $open .= '<em>';
            $close = '</em>' . $close;
        }
        if ($font->getUnderline() && $font->getUnderline() !== 'none') {
            $open .= '<u>';
            $close = '</u>' . $close;
        }

        return $open . $escaped . $close;
    }

    /**
     * Dernier montant « … 510 $ » d'une ligne de prix (séparateurs de milliers tolérés),
     * en ignorant le nombre de la durée.
     */
    private function lastAmount(string $text, int $duration): ?int
    {
        if (!preg_match_all('/([\d][\d\s,.]*)\s*\$/u', $text, $m)) {
            return null;
        }

        $raw = end($m[1]);
        $amount = (int)preg_replace('/\D/', '', $raw);

        return ($amount > 0 && $amount !== $duration) ? $amount : null;
    }

    /**
     * Reconnaît une province dans un en-tête de forfait (texte déjà « aplati »),
     * renvoie son label State (ON/QC/BC/…) ou NULL. Couvre les noms FR et EN.
     */
    private function provinceLabel(string $cFlat): ?string
    {
        static $map = [
            'ONTARIO'           => 'ON',
            'QUEBEC'            => 'QC',
            'COLOMBIE'          => 'BC', 'BRITISH COLUMBIA' => 'BC',
            'ALBERTA'           => 'AB',
            'MANITOBA'          => 'MB',
            'SASKATCHEWAN'      => 'SK',
            'NOUVELLE-ECOSSE'   => 'NS', 'NOVA SCOTIA'      => 'NS',
            'NOUVEAU-BRUNSWICK' => 'NB', 'NEW BRUNSWICK'    => 'NB',
            'TERRE-NEUVE'       => 'NL', 'NEWFOUNDLAND'     => 'NL',
            'PRINCE-EDOUARD'    => 'PE', 'PRINCE EDOUARD'   => 'PE', 'PRINCE EDWARD' => 'PE',
        ];

        foreach ($map as $keyword => $label) {
            if (str_contains($cFlat, $keyword)) {
                return $label;
            }
        }

        return null;
    }

    /**
     * Id de la State (province) par label, mémoïsé. Avertit si la province n'est pas seedée.
     */
    private function provinceStateId(string $label, array &$cache): ?int
    {
        if (array_key_exists($label, $cache)) {
            return $cache[$label];
        }

        $id = \App\Models\Core\State::where('label', $label)->value('id');
        if (!$id) {
            $this->warnings[] = "Province « {$label} » introuvable (exécuter ProvincesSeeder) : forfaits de cette province ignorés.";
        }

        return $cache[$label] = $id;
    }

    private function providerType(?string $clientele): ?string
    {
        $flat = $this->flatten($clientele ?? '');

        // RÉSIDENTIEL testé d'abord : certains fichiers anglais écrivent « B2BRESIDENTIAL »
        // (contient « B2B » mais désigne bel et bien le résidentiel).
        if (str_contains($flat, 'RESIDENTIEL') || str_contains($flat, 'RESIDENTIAL')) {
            return 'residential';
        }
        if (str_contains($flat, 'B2B') || str_contains($flat, 'BUSINESS') || str_contains($flat, 'AFFAIRE')) {
            return 'business';
        }

        return null;
    }

    /**
     * Normalise pour la détection de marqueurs : majuscules, accents retirés.
     */
    private function flatten(string $text): string
    {
        $text = mb_strtoupper(trim($text));

        return strtr($text, [
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'À' => 'A', 'Â' => 'A', 'Î' => 'I', 'Ï' => 'I',
            'Ô' => 'O', 'Û' => 'U', 'Ù' => 'U', 'Ç' => 'C',
        ]);
    }
}

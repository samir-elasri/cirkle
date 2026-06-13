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

    public function import(string $path): array
    {
        // Lecture sur une COPIE temporaire : la détection de format (ZipArchive)
        // peut réécrire/normaliser silencieusement le fichier source (+quelques octets).
        $tmp = tempnam(sys_get_temp_dir(), 'master2350_');
        if ($tmp === false || !copy($path, $tmp)) {
            throw new \RuntimeException("Impossible de copier le fichier d'import : {$path}");
        }

        try {
            return $this->importFromCopy($tmp);
        } finally {
            @unlink($tmp);
        }
    }

    private function importFromCopy(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $worksheet = $reader->load($path)->getSheet(0);
        $highestRow = $worksheet->getHighestDataRow();

        // ── Métadonnées de la fiche ──
        $clientele = $langue = $titreInterne = $categorie = $profession = null;

        $services = [];      // lignes « O » de SECTION SERVICES OFFERTS
        $capabilities = [];  // lignes « O » de SECTION PERSONALISE CAPABILITIES
        $customersText = []; // SECTION CUSTOMERS TEXT (formulaire standard)
        $capabilitiesText = [];
        $prices = [];        // 4 forfaits code postal : durée => coût
        $keywords = [];

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
            } elseif (str_starts_with($this->flatten($c), 'OBLIGATOIRE')) {
                // Bloc « FRAIS POUR LA FICHE DE COMPETENCE » : c'est la porte d'acceptation
                // des frais (feature #6), pas une capacité — traité dans sa propre tranche.
                $state = 'skip';
                continue;
            }

            // ── Collecte selon la section courante ──
            switch ($state) {
                case 'services':
                case 'capabilities':
                    if ($b === 'O') {
                        $html = $this->formattedText($cCell);
                        if (trim($c) === '' || $html === '') {
                            break; // entièrement rouge ou vide : disparaît
                        }
                        $entry = [
                            'title' => $c,
                            'formatted_title' => $html,
                            'has_input' => ($d === 'X'),
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

                case 'prices':
                    // Les 4 premiers « O » après le marqueur FORFAITS = 1/3/6/12 mois code postal.
                    // Les sections suivantes (provinces, site web) ne sont pas encore modélisées.
                    if ($b === 'O' && count($prices) < 4
                        && preg_match('/(\d+)\s*(MOIS|MONTH)/iu', $c, $m)) {
                        $duration = (int)$m[1];
                        $cost = $this->lastAmount($c, $duration);
                        if ($cost !== null) {
                            $prices[$duration] = $cost;
                        }
                    }
                    if (count($prices) >= 4) {
                        $state = 'skip'; // provinces / site web : hors modèle pour l'instant
                    }
                    break;

                case 'keywords':
                    if (trim($c) !== '') {
                        $keywords[] = trim($c);
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

        foreach ($prices as $duration => $cost) {
            $subscription = Subscription::where('duration', '=', $duration)->where('active', '=', true)->first();
            if (!$subscription) {
                $this->warnings[] = "Aucune Subscription active de {$duration} mois : prix {$cost}\$ ignoré.";
                continue;
            }
            $price = SubscriptionPrice::firstOrCreate([
                'service_category_id' => $categoryModel->id,
                'subscription_id' => $subscription->id,
            ]);
            $price->cost = $cost;
            $price->save();
        }

        return [
            'profession' => $profession,
            'provider_type' => $categoryModel->provider_type,
            'locale' => app()->getLocale(),
            'services' => count($services),
            'capabilities' => count($capabilities),
            'prices' => $prices,
            'keywords' => count($keywords),
            'warnings' => $this->warnings,
        ];
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

    private function providerType(?string $clientele): ?string
    {
        $flat = $this->flatten($clientele ?? '');

        if (str_contains($flat, 'B2B') || str_contains($flat, 'BUSINESS') || str_contains($flat, 'AFFAIRE')) {
            return 'business';
        }
        if (str_contains($flat, 'RESIDENTIEL') || str_contains($flat, 'RESIDENTIAL')) {
            return 'residential';
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

{{-- PAGE D'ACCUEIL — refonte compacte « tout sur une page » d'après la spec de Denis
     (docs/CIRKLE PAGE ACCUEIL 010626.xlsx) : direct au but, peu d'espace, en couleur.
     Bilingue via le helper local $t (FR par défaut / EN). --}}
@php
    $loc = app()->getLocale();
    $t = fn ($fr, $en) => $loc === 'en' ? $en : $fr;
    $eBadge = asset_with_version('/dist/img/cirkle-e-badge.png');
    // Plateforme choisie (résidentiel/B2B) : ne montrer que les professions de cette
    // plateforme (la langue FR/EN est déjà gérée par la traduction du titre). Évite que
    // les 4 variantes (W…RF/RE/B2BE/B2BF) d'un même service apparaissent ensemble.
    $platformType = $selectedProviderType ?? 'residential';
    // Catalogue complet (noir) : catégories (parents) -> professions (sous-catégories)
    $profsByCat = ($subcategories ?? collect())->groupBy('service_category_id');
    // Caricatures « sur les côtés » (Denis 11.07) = les MÊMES images que le
    // carrousel du haut (slides actifs de l'admin). Vide tant qu'aucune n'est ajoutée.
    $sideCaricatures = \App\Models\Core\Slide::where('active', true)
        ->whereNotNull('image')->where('image', '!=', '')
        ->orderBy('position')->pluck('image');
@endphp

@include('partials.side-caricatures', ['caricatures' => $sideCaricatures])

<section class="ck-home">
    <div class="optimal-content-width">

        {{-- Sélecteur des 4 plateformes (résidentiel/B2B × FR/EN) — choix mis en évidence en jaune --}}
        @include('partials.platform-selector')

        {{-- Bannières : promotion, lancement, recrutement + suggestion --}}
        <p class="ck-home__banner ck-home__banner--promo">{{ $t('Promotion : avant le 1er octobre 2026, vous recevrez un crédit de 100,00 $.', 'Promotion: before October 1, 2026, you will receive a $100.00 credit.') }}</p>
        <p class="ck-home__banner ck-home__banner--launch">{{ $t('Lancement officiel prévu pour le 1er octobre 2026.', 'Official launch planned for October 1, 2026.') }}</p>
        <p class="ck-home__banner ck-home__banner--recruit">{{ $t("Notre processus de recrutement « perpétuel » des fournisseurs se poursuit afin d'élargir notre réseau de partenaires.", 'Our ongoing supplier recruitment continues in order to grow our network of partners.') }}</p>
        <p class="ck-home__suggestion">{{ $t("Suggestion : profitez de votre présence pour naviguer Cirkle afin d'anticiper, prévoir et budgéter vos prochains services et/ou vos futurs projets.", 'Suggestion: use your visit to browse Cirkle to anticipate, plan and budget your next services and future projects.') }}</p>

        {{-- 3 colonnes compactes : Avantages clients | Bienvenue (clics + recherche) | Avantages fournisseurs --}}
        <div class="ck-home__cols">

            {{-- ─── Colonne 1 : Avantages membres CLIENTS (col I de la spec) ─── --}}
            <div class="ck-home__col">
                <h3 class="ck-home__h">{{ $t('Avantages membres clients', 'Client member benefits') }}</h3>
                <p class="ck-home__sub">{{ $t('2 clics et connexion avec les fournisseurs', '2 clicks and you are connected with suppliers') }}</p>
                <p class="ck-home__step">{{ $t('Réduisez votre temps de recherche. Recevez les informations des professionnels :', 'Cut your search time. Get the professionals’ information:') }}</p>
                <ul class="ck-home__list ck-home__list--client">
                    <li>{{ $t('Toutes leurs compétences', 'All their skills') }}</li>
                    <li>{{ $t('Leurs PROMOTIONS FOURNISSEUR', 'Their SUPPLIER PROMOTIONS') }} <span class="ck-promo-badge">PROMO</span></li>
                    <li>{{ $t('Leur système d’évaluation', 'Their rating system') }}</li>
                    <li>{{ $t('Leurs photos, diplômes et permis', 'Their photos, diplomas and licenses') }}</li>
                    <li>{{ $t('Leurs recrutements d’employé(e)s', 'Their staff recruitment') }} <img class="ck-e-badge" src="{{ $eBadge }}" alt="(e)"></li>
                    <li>{{ $t('Comparer 2 fournisseurs ou plus', 'Compare 2 or more suppliers') }}</li>
                    <li>{{ $t('Satisfaction, fiabilité, qualité des informations', 'Satisfaction, reliability, quality of information') }}</li>
                    <li>{{ $t('Service personnalisé et innovation', 'Personalized service and innovation') }}</li>
                    <li>{{ $t('Vos fournisseurs, catégories et professions favoris', 'Your favourite suppliers, categories and professions') }}</li>
                </ul>
                <a class="ck-home__cta ck-home__cta--client" href="{{ setting('home_client_link2_url') ?: url($loc.'/sinscrire') }}">{{ $t('Devenez membre client', 'Become a client member') }}</a>
            </div>

            {{-- ─── Colonne 2 : BIENVENUE — les clics + la recherche (col E de la spec) ─── --}}
            <div class="ck-home__col ck-home__col--center">
                <h3 class="ck-home__h">{{ $t('Bienvenue', 'Welcome') }}</h3>
                <p class="ck-home__sub">{{ $t('Aux clients membres et non-membres', 'To member and non-member clients') }}</p>
                <p class="ck-home__step"><span class="ck-home__blue">{{ $t('1er clic', '1st click') }} :</span> {{ $t('votre code postal ci-dessous (ou avoisinant, ou ailleurs). Cirkle téléchargera automatiquement', 'your postal code below (nearby or elsewhere). Cirkle will automatically load') }} <span class="ck-home__green">{{ $t('la liste des professions disponibles en vert', 'the list of available professions in green') }}</span>.</p>

                {{-- Bloc code postal directement sous le « 1er clic » (Denis 21.06). Le résultat
                     (professions en vert) s'affiche dans .postalCodeSearch__result. --}}
                <div class="ck-home__postal">
                    @include('partials.providers.public-search-filters')
                </div>

                <p class="ck-home__step"><span class="ck-home__blue">{{ $t('2e clic', '2nd click') }} :</span> {{ $t('sur la profession de votre choix — Cirkle vous connecte directement avec les fournisseurs membres.', 'on the profession of your choice — Cirkle connects you directly with member suppliers.') }}</p>
                <div class="ck-home__warn">{{ $t('Important : toute communication à l’extérieur de Cirkle n’est plus la responsabilité de cirkleservices.com.', 'Important: any communication outside Cirkle is no longer the responsibility of cirkleservices.com.') }}</div>
                <div class="ck-home__legend ck-home__legend--recruit"><img class="ck-e-badge" src="{{ $eBadge }}" alt="(e)"> = {{ $t('un ou plusieurs fournisseurs recrutent du personnel', 'one or more suppliers are recruiting staff') }}</div>
                <div class="ck-home__legend ck-home__legend--promo"><span class="ck-promo-badge">PROMO</span> = {{ $t('PROMOTION FOURNISSEUR : un ou plusieurs fournisseurs offrent une promotion', 'SUPPLIER PROMOTION: one or more suppliers are offering a promotion') }}</div>
            </div>

            {{-- ─── Colonne 3 : Avantages membres FOURNISSEURS (col G de la spec) ─── --}}
            <div class="ck-home__col">
                <h3 class="ck-home__h">{{ $t('Avantages membres fournisseurs', 'Supplier member benefits') }}</h3>
                <ul class="ck-home__list ck-home__list--supplier">
                    <li>{{ $t('Hébergement : frais inclus', 'Hosting: fees included') }}</li>
                    <li>{{ $t('Référencement : inclus (SEO, mots-clés)', 'SEO: included (keywords)') }}</li>
                    <li>{{ $t('Programmation et publicité : frais inclus', 'Development and advertising: fees included') }}</li>
                    <li>{{ $t('Courriel aux clients : nouveau fournisseur / nouvelle promotion', 'Email to clients: new supplier / new promotion') }}</li>
                    <li>{{ $t('Visibilité, partenariat, collaboration, croissance', 'Visibility, partnership, collaboration, growth') }}</li>
                    <li>{{ $t('Expansion de votre réseau et accompagnement', 'Grow your network, with support') }}</li>
                </ul>
                <p class="ck-home__sub">{{ $t('« Choix de 6 options »', '“Choice of 6 options”') }}</p>
                <ul class="ck-home__list ck-home__list--supplier">
                    <li>{{ $t('Vos PROMOTIONS FOURNISSEUR', 'Your SUPPLIER PROMOTIONS') }} <span class="ck-promo-badge">PROMO</span></li>
                    <li>{{ $t('Offre d’emploi / recrutement', 'Job offer / recruiting') }} <img class="ck-e-badge" src="{{ $eBadge }}" alt="(e)"></li>
                    <li>{{ $t('12 photos', '12 photos') }}</li>
                    <li>{{ $t('Votre formulaire d’estimation', 'Your estimate form') }}</li>
                    <li>{{ $t('Vos diplômes académiques', 'Your academic diplomas') }}</li>
                    <li>{{ $t('Vos permis, licences et associations', 'Your licenses & associations') }}</li>
                </ul>
                <a class="ck-home__cta" href="{{ setting('home_provider_link2_url') ?: url($loc.'/sinscrire/fournisseur') }}">{{ $t('Devenez membre fournisseur', 'Become a supplier member') }}</a>
            </div>
        </div>

        {{-- Catalogue complet (noir) : résume toutes les professions de Cirkle (spec Denis) --}}
        <div class="ck-home__catalogue">
            <p class="ck-home__catalogue-note">{{ $t('Recrutement continu en cours — liste complète des professions actuellement disponibles sur Cirkle :', 'Ongoing recruitment — complete list of professions currently available on Cirkle:') }}</p>
            {{-- Denis 24.06 : « la ligne FOURNISSEURS … servclient@ : le font + gros ». --}}
            <p class="ck-home__catalogue-contact" style="font-size:1.15em;font-weight:700">{{ $t('Fournisseurs : si votre profession ne figure pas dans la liste, écrivez-nous à servclient@cirkleservices.com', 'Suppliers: if your profession is not listed, email us at servclient@cirkleservices.com') }}</p>

            @foreach ($categories as $category)
                @php $profs = ($profsByCat[$category->id] ?? collect())->filter(fn ($p) => !empty($p->title) && (($p->provider_type ?: 'residential') === $platformType)); @endphp
                @if (!$category->title || $profs->isEmpty()) @continue @endif
                <div class="ck-home__cat-row">
                    <div class="ck-home__cat">{{ $category->title }}</div>
                    <div class="ck-home__profs">
                        {{-- Catalogue noir = liste de référence de TOUTES les professions de Cirkle,
                             NON cliquable (Denis 01.07). Le client clique plutôt sur les professions
                             en VERT qui apparaissent sous son code postal (partials/search). --}}
                        @foreach ($profs as $prof)
                            <span class="ck-home__prof">{{ $prof->title }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{!! $blocs !!}

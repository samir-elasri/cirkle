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
    // Caricatures par profession (Denis 22.07 — doc « CARICATURES PROFESSION PAR CHAT »).
    // Chaque planche montre 3 personnages : c'est déjà un « groupe de 3 » (spec Denis :
    // « groupes de 3 images pour un look plus équilibré »). On utilise les versions web
    // allégées (/pro-web, ~80 Ko chacune) pour ne pas alourdir l'accueil.
    $caricTiles = [
        'caric-01', 'caric-02', 'caric-06', 'caric-07', 'caric-08', 'caric-10',
        'caric-12', 'caric-14', 'caric-15', 'caric-17', 'caric-18', 'caric-19',
    ];
    // Phrase courte du site (Denis 22.07 : « 5 à 10 mots max »).
    $ckTagline = $t('Le bon fournisseur, en 2 à 3 clics.', 'The right pro, in just 2–3 clicks.');
@endphp

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

            {{-- Catalogue en BLOCS par catégorie (Denis 16.07 : « plus prof, moins
                 tassé, dans des blocs… rajouter des infos ex. caricatures »). Chaque
                 catégorie = une carte (même style que les colonnes du haut) avec un
                 emplacement d'icône/caricature, un compteur, et ses professions en
                 pastilles. Liste de RÉFÉRENCE non cliquable (Denis 01.07) — le vert
                 cliquable apparaît après le code postal (partials/search). --}}
            <style>
                .ck-cat-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(330px,1fr)); gap:16px; margin-top:.7em; }
                .ck-cat-card { background:#fff; border:1px solid rgba(0,0,0,.06); border-radius:16px; padding:16px 18px; box-shadow:0 10px 26px rgba(22,27,38,.07); }
                .ck-cat-card__head { display:flex; align-items:center; gap:11px; margin-bottom:12px; padding-bottom:10px; border-bottom:2px solid #eaf3ec; }
                .ck-cat-card__icon { width:40px; height:40px; flex:0 0 40px; border-radius:50%; object-fit:cover;
                    background:linear-gradient(135deg,#e9f6ee,#d5efdd); display:flex; align-items:center; justify-content:center; }
                .ck-cat-card__icon svg { width:20px; height:20px; fill:#00893e; }
                .ck-cat-card__title { font-weight:800; color:#00893e; font-size:1.02rem; letter-spacing:.3px; text-transform:uppercase; }
                .ck-cat-card__count { margin-left:auto; background:#eef6f0; color:#3c7a52; font-weight:700; font-size:.78rem; border-radius:999px; padding:3px 10px; white-space:nowrap; }
                .ck-cat-card__profs { display:flex; flex-wrap:wrap; gap:7px 8px; }
                .ck-cat-chip { background:#f5f8f5; border:1px solid #e6ece6; color:#1f2a22; font-size:.83rem; border-radius:9px; padding:5px 10px; line-height:1.2; }
                @media (max-width:560px){ .ck-cat-cards { grid-template-columns:1fr; } }
            </style>
            <div class="ck-cat-cards">
                @foreach ($categories as $category)
                    @php $profs = ($profsByCat[$category->id] ?? collect())->filter(fn ($p) => !empty($p->title) && (($p->provider_type ?: 'residential') === $platformType)); @endphp
                    @if (!$category->title || $profs->isEmpty()) @continue @endif
                    <div class="ck-cat-card">
                        <div class="ck-cat-card__head">
                            {{-- Emplacement icône/caricature de la catégorie (Denis : « rajouter
                                 des infos dans ces blocs ») : la caricature si définie, sinon une
                                 pastille verte avec une icône générique. --}}
                            @if (!empty($category->image))
                                <img class="ck-cat-card__icon" src="{{ $category->image }}" alt="">
                            @else
                                <span class="ck-cat-card__icon"><svg viewBox="0 0 24 24"><path d="M12 2 3 7v10l9 5 9-5V7l-9-5zm0 2.2 6.5 3.6L12 11.4 5.5 7.8 12 4.2zM5 9.3l6 3.3v7.1l-6-3.3V9.3zm14 0v7.1l-6 3.3v-7.1l6-3.3z"/></svg></span>
                            @endif
                            <span class="ck-cat-card__title">{{ $category->title }}</span>
                            <span class="ck-cat-card__count">{{ $profs->count() }}</span>
                        </div>
                        <div class="ck-cat-card__profs">
                            @foreach ($profs as $prof)
                                <span class="ck-cat-chip">{{ $prof->title }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Phrase courte + slider de caricatures « groupes de 3 » (Denis 22.07).
     Chaque planche = 3 personnages ; elles défilent doucement et en continu vers la
     DROITE — bannière dynamique discrète qui « donne vie » au site. Tuiles dupliquées
     pour une boucle sans couture ; pause au survol ; fondus sur les bords. --}}
@if(!empty($caricTiles))
<section class="ck-caric">
    <style>
        .ck-caric { width:100%; overflow:hidden; background:transparent; margin-top:34px; padding:0 0 10px; }
        .ck-caric__tagline { text-align:center; font-size:clamp(1.15rem,2.4vw,1.6rem); font-weight:900;
            color:#00893e; margin:0 auto 16px; padding:0 16px; max-width:900px; line-height:1.25; text-wrap:balance; }
        .ck-caric__viewport { width:100%; overflow:hidden;
            -webkit-mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent);
            mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent); }
        .ck-caric__track { display:flex; gap:18px; width:max-content; padding:0 9px;
            animation:ck-caric-slide 70s linear infinite; }
        .ck-caric:hover .ck-caric__track { animation-play-state:paused; }
        .ck-caric__tile { flex:0 0 auto; width:clamp(300px, 46vw, 520px); }
        .ck-caric__tile img { width:100%; height:auto; display:block; border-radius:16px;
            border:1px solid #e2e8e2; box-shadow:0 8px 22px rgba(22,27,38,.12); background:#fff; }
        /* départ décalé d'une copie → mouvement vers la DROITE, boucle sans couture */
        @keyframes ck-caric-slide { from { transform:translateX(-50%); } to { transform:translateX(0); } }
        @media (max-width:560px){ .ck-caric__tile { width:80vw; } .ck-caric__track { gap:12px; } }
        @media (prefers-reduced-motion: reduce){ .ck-caric__track { animation:none; } }
    </style>
    <p class="ck-caric__tagline">{{ $ckTagline }}</p>
    <div class="ck-caric__viewport">
        <div class="ck-caric__track">
            @foreach(array_merge($caricTiles, $caricTiles) as $tile)
                <div class="ck-caric__tile"><img loading="lazy" src="{{ asset_with_version('/dist/img/caricatures/pro-web/'.$tile.'.jpg') }}" alt=""></div>
            @endforeach
        </div>
    </div>
</section>
@endif

{!! $blocs !!}

{{-- PAGE IDÉOLOGIE / ENGAGEMENT — contenu fourni par Denis
     (docs/WEB CIRKLE IDEOLOGIE ENGAGEMENT 20.06.25). Rendu dans la mise en page du site. --}}
@php
    $loc = app()->getLocale(); $t = fn ($fr, $en) => $loc === 'en' ? $en : $fr;
    // Caricatures « groupes de 3 » (Denis 22.07 : « ajustement du slider et des images »).
    $caricTiles = ['caric-01', 'caric-08', 'caric-17', 'caric-19', 'caric-10', 'caric-02'];
@endphp

<section class="ck-ideo">
    <div class="optimal-content-width">
        <style>
            .ck-ideo { padding: 8px 0 40px; }
            .ck-ideo__title { text-align:center; font-size:1.9rem; font-weight:900; color:#00993a; margin:.3em 0 1em; }
            .ck-ideo h2 { background:#a8f0ef; display:inline-block; padding:.15em .55em; border-radius:6px;
                font-weight:800; font-size:1.15rem; margin:1.6em 0 .5em; color:#0b3d39; }
            .ck-ideo h3 { color:#0070C0; font-weight:800; font-size:1.05rem; margin:1.2em 0 .3em; }
            .ck-ideo p { line-height:1.65; margin:.5em 0; color:#1f2733; }
            .ck-ideo .lead { color:#00993a; font-weight:700; }
            .ck-ideo ul { margin:.4em 0 .8em 1.2em; line-height:1.6; }
            .ck-ideo .ck-ck { color:#e8a900; font-weight:800; }
            .ck-ideo hr { border:none; border-top:1px solid #e0e4ec; margin:1.6em 0; }
        </style>

        <h1 class="ck-ideo__title">{{ $t('Idéologie & Engagements', 'Ideology & Commitments') }}</h1>

        <h2>{{ $t('Idéologie du site', 'Site ideology') }} <span class="ck-ck">CIRKLESERVICES.COM</span></h2>
        <p class="lead">CIRKLE encourage des transactions respectueuses, transparentes et menées de bonne foi, favorisant des échanges équitables, durables et positifs pour l'ensemble de ses membres.</p>
        <p>Bienvenue au sein de la communauté CIRKLE, un environnement professionnel fondé sur la confiance, la collaboration et la création d'opportunités à l'échelle nationale.</p>
        <p>En tant que membre actif, vous devenez un partenaire clé de notre réseau professionnel. En rejoignant CIRKLE, vous bénéficiez d'avantages exclusifs : une visibilité accrue auprès d'une clientèle qualifiée, une portée marketing élargie à travers le Canada, ainsi que des outils performants conçus pour optimiser votre présence en ligne et renforcer durablement votre référencement naturel.</p>

        <h3>Facilitation des échanges</h3>
        <p>CIRKLE centralise les besoins des clients et l'offre des fournisseurs de services au sein d'une plateforme unique, structurée et performante.</p>
        <h3>Valorisation des échanges locaux</h3>
        <p>La plateforme favorise les interactions économiques aux niveaux local, municipal, provincial et fédéral, contribuant activement au développement des communautés et des entreprises canadiennes.</p>
        <h3>Rapidité et efficacité de mise en relation</h3>
        <p>Grâce à un processus simplifié, 3 clics suffisent pour entrer en contact avec un fournisseur de services membre.</p>
        <h3>Services offerts</h3>
        <p>Reposant sur les principes de l'économie du partage, CIRKLE permet une connexion directe, rapide et transparente entre les clients à la recherche de services et les professionnels mettant leur expertise à disposition.</p>

        <p>Nous vous invitons à devenir membre dès maintenant, afin de planifier et de structurer efficacement vos activités tout au long de l'année, en tenant compte des périodes clés suivantes :</p>
        <ul>
            <li><strong>PRINTEMPS</strong> : du 1er mars au 31 mai</li>
            <li><strong>ÉTÉ</strong> : du 1er juin au 31 août</li>
            <li><strong>AUTOMNE</strong> : du 1er septembre au 30 novembre</li>
            <li><strong>HIVER</strong> : du 1er décembre au 28 février</li>
        </ul>
        <p>Pour adhérer, visitez <span class="ck-ck">www.cirkleservices.com</span> ou communiquez avec notre équipe à <strong>servclient@cirkleservices.com</strong>.</p>

        <hr>
        <h2>Engagements de <span class="ck-ck">CIRKLESERVICES.COM</span></h2>
        <p>CIRKLE encourage des transactions respectueuses, transparentes et menées de bonne foi, dans le but de favoriser des échanges positifs, équitables et durables pour l'ensemble de ses membres.</p>

        <h3>1 de 3 — Clients</h3>
        <p>La création de CIRKLESERVICES.COM repose avant tout sur la volonté d'avantager les clients en réduisant considérablement le temps et les efforts requis pour trouver un professionnel qualifié répondant précisément à leurs besoins.</p>
        <p>Traditionnellement, les clients devaient comparer plusieurs fournisseurs, répéter les mêmes questions et effectuer de longues recherches sans disposer d'informations fiables et structurées. CIRKLE met fin à cette complexité en offrant un accès centralisé à des professionnels sélectionnés, permettant aux clients de faire un choix éclairé dès le départ.</p>

        <h3>2 de 3 — Fournisseurs</h3>
        <p>Selon Statistique Canada, au 1er avril 2025, le Canada compte plus de 41 millions d'habitants, chacun ayant besoin, à répétition, de services variés. CIRKLE offre ainsi aux fournisseurs une présence stratégique sur le web, assurant une visibilité ciblée auprès d'une clientèle pertinente.</p>
        <p>CIRKLE propose une solution efficace, abordable et accessible, permettant aux fournisseurs d'être contactés directement et instantanément par des clients à la recherche de services spécifiques — notamment grâce à des critères précis tels que les codes postaux. <strong>CIRKLE connecte clients et fournisseurs instantanément.</strong></p>
        <p>La plateforme CIRKLE propose quatre options adaptées aux différents marchés :</p>
        <ul>
            <li>Résidentiel en français</li>
            <li>Residential in English</li>
            <li>B2B Affaires en français</li>
            <li>B2B Business in English</li>
        </ul>

        <h3>3 de 3 — Informations détaillées des fournisseurs à l'intention des clients</h3>
        <p>Les fournisseurs ont la possibilité de coopérer volontairement en partageant leurs informations professionnelles, dans un objectif de transparence et de confiance :</p>
        <ul>
            <li>Nom légal</li>
            <li>Adresse, téléphone et courriel</li>
            <li>Numéro de taxe fédérale</li>
            <li>Date d'enregistrement auprès des autorités gouvernementales</li>
            <li>Estimations détaillées</li>
            <li>Galerie de photos (6 à 12 images)</li>
            <li>Recrutement d'employés</li>
            <li>Promotions en cours</li>
            <li>Permis détenus</li>
            <li>Diplômes et formations académiques</li>
        </ul>

        <hr>
        <h2>Fiches de compétences professionnelles</h2>
        <p>Structure de communication entre clients et fournisseurs — tous les choix et espaces ont été conçus et structurés par CIRKLE :</p>
        <ul>
            <li><strong>1er espace</strong> — CIRKLE définit et propose les catégories et noms des services.</li>
            <li>Le fournisseur sélectionne les services qu'il est en mesure d'offrir, et peut ajouter des services complémentaires ainsi que des informations pertinentes pour les clients.</li>
            <li><strong>2e espace</strong> — CIRKLE fournit des informations normalisées et pertinentes liées au service concerné.</li>
            <li><strong>3e espace</strong> — l'ensemble des détails relatifs aux services : types d'édifices concernés, nom et numéro de membre du fournisseur, services secondaires associés (évaluation, inspection, diagnostic, garanties, financement, modalités de paiement, etc.).</li>
            <li><strong>4e espace</strong> — dédié aux frais, tarifs, ventes de produits et autres informations financières communiquées par le fournisseur.</li>
            <li><strong>5e espace</strong> — espace libre permettant aux fournisseurs de transmettre toute information additionnelle pertinente aux clients.</li>
        </ul>

        <hr>
        <p class="lead" style="text-align:center">Bienvenue au sein de la communauté CIRKLE, où les opportunités sont infinies.</p>
        <p style="text-align:center"><span class="ck-ck">CIRKLESERVICES.COM</span></p>
    </div>
</section>

{{-- Bande de caricatures « groupes de 3 » (Denis 22.07) : quelques planches qui
     défilent doucement pour illustrer la page. Versions web légères (/pro-web). --}}
@if(!empty($caricTiles))
<section class="ck-ideo-caric">
    <style>
        .ck-ideo-caric { width:100%; overflow:hidden; background:transparent; margin:8px 0 40px; }
        .ck-ideo-caric__viewport { width:100%; overflow:hidden;
            -webkit-mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent);
            mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent); }
        .ck-ideo-caric__track { display:flex; gap:16px; width:max-content; padding:0 8px;
            animation:ck-ideo-caric-slide 60s linear infinite; }
        .ck-ideo-caric:hover .ck-ideo-caric__track { animation-play-state:paused; }
        .ck-ideo-caric__tile { flex:0 0 auto; width:clamp(260px, 40vw, 460px); }
        .ck-ideo-caric__tile img { width:100%; height:auto; display:block; border-radius:16px;
            border:1px solid #e2e8e2; box-shadow:0 8px 22px rgba(22,27,38,.12); background:#fff; }
        @keyframes ck-ideo-caric-slide { from { transform:translateX(-50%); } to { transform:translateX(0); } }
        @media (max-width:560px){ .ck-ideo-caric__tile { width:78vw; } }
        @media (prefers-reduced-motion: reduce){ .ck-ideo-caric__track { animation:none; } }
    </style>
    <div class="ck-ideo-caric__viewport">
        <div class="ck-ideo-caric__track">
            @foreach(array_merge($caricTiles, $caricTiles) as $tile)
                <div class="ck-ideo-caric__tile"><img loading="lazy" src="{{ asset_with_version('/dist/img/caricatures/pro-web/'.$tile.'.jpg') }}" alt=""></div>
            @endforeach
        </div>
    </div>
</section>
@endif

{!! $blocs ?? '' !!}

{{-- PAGE CONCLUSION (docs/WEB04F PAGE CONCLUSION 010626) — lien obligatoire au bas du
     2350 (Denis 24.06). Page autonome bilingue + bouton retour. --}}
@php
    $loc = app()->getLocale();
    $t = fn ($fr, $en) => $loc === 'en' ? $en : $fr;
    $logo = setting('main_logo_image');
@endphp
<!DOCTYPE html>
<html lang="{{ $loc }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $t('Conclusion — Cirkle', 'Conclusion — Cirkle') }}</title>
    <link rel="icon" type="image/png" href="/favicon-cirkle.png">
    <link rel="stylesheet" href="{{ asset_with_version('/dist/compiled/modern.css') }}">
    <style>
        body { margin:0; font-family: system-ui,"Segoe UI",Roboto,Arial,sans-serif; color:#1f2733; background:#f4f6fb; line-height:1.6; }
        .cc-top { display:flex; align-items:center; gap:14px; padding:10px 20px; background:#fff; border-bottom:1px solid #e6e9f0; box-shadow:0 4px 16px rgba(22,27,38,.06); position:sticky; top:0; }
        .cc-top img { max-height:42px; width:auto; }
        .cc-back { margin-left:auto; background:#ffd200; color:#161b26; font-weight:800; text-decoration:none; padding:9px 18px; border-radius:8px; border:0; cursor:pointer; }
        .cc-wrap { max-width:820px; margin:24px auto 60px; padding:0 18px; }
        .cc-title { text-align:center; font-size:2rem; font-weight:900; margin:.2em 0 1em; }
        .cc-h { background:#a8f0ef; display:inline-block; padding:.15em .5em; border-radius:6px; font-weight:800; font-size:1.05rem; margin:1.4em 0 .5em; }
        .cc-lead { color:#00993a; font-weight:800; font-size:1.15rem; }
        .cc-strong { color:#00993a; font-weight:700; }
        .cc-wrap p { margin:.5em 0; }
        .cc-rule { border:none; border-top:1px solid #e0e4ec; margin:1.4em 0; }
        .cc-cirkle { color:#e8a900; font-weight:800; }
        .cc-bottomback { display:inline-block; margin-top:1.6em; }
    </style>
</head>
<body>
    <div class="cc-top">
        @if($logo)<img src="{{ $logo }}" alt="Cirkle">@endif
        <button type="button" class="cc-back" onclick="window.close(); if(window.history.length>1){window.history.back();}">← {{ $t('Retour', 'Back') }}</button>
    </div>

    <div class="cc-wrap">
        <h1 class="cc-title">🎉 {{ $t('CONCLUSION', 'CONCLUSION') }} 🎉</h1>

        <p class="cc-lead">{{ $t('Merci de votre inscription', 'Thank you for registering') }}</p>
        <p><strong>{{ $t('Fournisseur,', 'Supplier,') }}</strong></p>
        <p>{{ $t("Vous avez complété votre formulaire d'inscription et nous vous remercions sincèrement pour votre temps, votre collaboration ainsi que la confiance que vous accordez à", 'You have completed your registration form, and we sincerely thank you for your time, your collaboration, and the trust you place in') }} <span class="cc-cirkle">CirkleServices.com</span>.</p>
        <p class="cc-strong">{{ $t("Votre participation contribue à la création d'une plateforme professionnelle visant à faciliter la mise en relation entre les clients et les fournisseurs de services partout au Canada.", 'Your participation helps build a professional platform that connects clients with service providers across Canada.') }}</p>

        <hr class="cc-rule">
        <div class="cc-h">{{ $t('ATTRIBUTION DES DEMANDES DE CLIENTS', 'DISTRIBUTION OF CLIENT REQUESTS') }}</div>
        <p>{{ $t("Afin de favoriser l'équité entre les fournisseurs membres et d'éviter toute situation préférentielle, CIRKLE utilisera un système de distribution impartial des connexions et des demandes de renseignements provenant des clients.", 'To ensure fairness among member suppliers and avoid any preferential treatment, CIRKLE uses an impartial system to distribute client connections and inquiries.') }}</p>
        <p>{{ $t("Notre objectif est d'offrir à tous les fournisseurs membres une opportunité équitable de présenter leurs services aux clients.", 'Our goal is to give every member supplier a fair opportunity to present their services to clients.') }}</p>

        <hr class="cc-rule">
        <div class="cc-h">{{ $t('VOTRE VISIBILITÉ SUR CIRKLE', 'YOUR VISIBILITY ON CIRKLE') }}</div>
        <p>{{ $t("Nous sommes convaincus que les informations que vous avez fournies aideront les clients à mieux comprendre vos compétences, votre expérience et votre professionnalisme.", 'We are confident that the information you provided will help clients better understand your skills, experience and professionalism.') }}</p>
        <p>{{ $t("Votre transparence, votre collaboration et la qualité de votre présentation contribueront à faciliter le processus décisionnel des clients tout en augmentant vos possibilités d'obtenir de nouvelles opportunités d'affaires.", 'Your transparency, collaboration and the quality of your presentation will make clients’ decisions easier while increasing your chances of new business opportunities.') }}</p>
        <p>{{ $t('Nous espérons que votre adhésion à CIRKLE sera bénéfique et rentable pour toutes les parties.', 'We hope your membership in CIRKLE will be beneficial and profitable for everyone.') }}</p>

        <hr class="cc-rule">
        <div class="cc-h">{{ $t('ACTIVATION DE VOTRE FICHE FOURNISSEUR', 'ACTIVATION OF YOUR SUPPLIER PROFILE') }}</div>
        <p>{{ $t('À la réception du paiement complet applicable et après validation administrative, votre fiche fournisseur sera intégrée à la plateforme', 'Upon receipt of the applicable full payment and after administrative validation, your supplier profile will be added to') }} <span class="cc-cirkle">CirkleServices.com</span>.</p>
        <p>{{ $t("Votre profil sera alors visible aux clients et prêt à recevoir des demandes de renseignements et des opportunités d'affaires.", 'Your profile will then be visible to clients and ready to receive inquiries and business opportunities.') }}</p>
        <p>{{ $t('Nous vous souhaitons beaucoup de succès dans le développement de vos activités.', 'We wish you great success in growing your business.') }}</p>

        <hr class="cc-rule">
        <div class="cc-h">{{ $t('VOTRE INSCRIPTION CLIENT AUTOMATIQUE', 'YOUR AUTOMATIC CLIENT REGISTRATION') }}</div>
        <p>{{ $t("Puisque la plupart des entreprises ont également besoin de services offerts par d'autres fournisseurs, CIRKLE vous inscrira automatiquement comme membre client lors de votre adhésion.", 'Since most businesses also need services from other suppliers, CIRKLE automatically registers you as a client member when you join.') }}</p>
        <p class="cc-strong">{{ $t('IMPORTANT', 'IMPORTANT') }}</p>
        <p class="cc-strong">{{ $t("Votre inscription client comprendra uniquement les renseignements de base de votre fiche d'inscription.", 'Your client registration will include only the basic information from your registration form.') }}</p>
        <p>{{ $t('Les fiches de compétences, profils professionnels, promotions, photos, documents et autres informations associées à votre profil fournisseur ne seront pas reproduits dans votre profil client.', 'Your competence sheets, professional profiles, promotions, photos, documents and other information tied to your supplier profile will not be copied into your client profile.') }}</p>

        <hr class="cc-rule">
        <div class="cc-h">{{ $t('MISE À JOUR DE VOS INFORMATIONS', 'UPDATING YOUR INFORMATION') }}</div>
        <p class="cc-strong">{{ $t("Le fournisseur demeure entièrement responsable de l'exactitude des renseignements affichés sur sa fiche.", 'The supplier remains fully responsible for the accuracy of the information shown on their profile.') }}</p>
        <p>{{ $t("Tout ajout, modification, correction, mise à jour ou changement d'adresse devra être effectué par le fournisseur sur les fiches correspondantes établies par CIRKLE.", 'Any addition, change, correction, update or change of address must be made by the supplier on the corresponding profiles set up by CIRKLE.') }}</p>

        <hr class="cc-rule">
        <p class="cc-strong">{{ $t('Merci de votre confiance et bienvenue dans la communauté CIRKLE.', 'Thank you for your trust and welcome to the CIRKLE community.') }}</p>
        <p class="cc-strong">{{ $t('Nous vous souhaitons beaucoup de succès !', 'We wish you great success!') }}</p>
        <p style="margin-top:1em">servclient@cirkleservices.com</p>

        <button type="button" class="cc-back cc-bottomback" onclick="window.close(); if(window.history.length>1){window.history.back();}">← {{ $t('Retour au formulaire', 'Back to the form') }}</button>
    </div>
</body>
</html>

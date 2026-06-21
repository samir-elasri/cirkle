{{-- PAGE « MASTER » — aperçu de tous les formulaires du site en onglets, accessible par
     lien direct SANS inscription (demande Denis 21.06). Page autonome (onglets en pur CSS,
     sans JS car le bundle est gelé) : chaque onglet affiche le vrai formulaire via iframe. --}}
@php
    $loc = app()->getLocale();
    $t = fn ($fr, $en) => $loc === 'en' ? $en : $fr;
    $u = fn ($path) => url($loc.'/'.ltrim($path, '/'));
    $logo = setting('main_logo_image');
@endphp
<!DOCTYPE html>
<html lang="{{ $loc }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $t('Master des formulaires — Cirkle', 'Master of forms — Cirkle') }}</title>
    <link rel="icon" type="image/png" href="/favicon-cirkle.png">
    <style>
        :root { --mf-green:#00b050; --mf-ink:#161b26; --mf-line:#e6e9f0; --mf-accent:#ffd200; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, "Segoe UI", Roboto, Arial, sans-serif; color:var(--mf-ink); background:#f4f6fb; }
        .mf-top { display:flex; align-items:center; gap:14px; padding:10px 20px; background:#fff; border-bottom:1px solid var(--mf-line); box-shadow:0 4px 16px rgba(22,27,38,.06); }
        .mf-top img { max-height:42px; width:auto; }
        .mf-top h1 { font-size:1.1rem; margin:0; font-weight:800; }
        .mf-top .mf-home { margin-left:auto; text-decoration:none; color:var(--mf-green); font-weight:700; font-size:.9rem; }
        .mf-note { max-width:1100px; margin:14px auto 0; padding:0 16px; color:#5a6472; font-size:.9rem; }

        .mf { max-width:1180px; margin:12px auto 24px; padding:0 16px; }
        .mf-radio { position:absolute; opacity:0; pointer-events:none; }
        .mf-tabbar { display:flex; flex-wrap:wrap; gap:6px; border-bottom:2px solid var(--mf-line); margin-bottom:0; }
        .mf-tabbar label { cursor:pointer; padding:10px 16px; font-weight:700; font-size:.92rem; color:#5a6472;
            border:1px solid var(--mf-line); border-bottom:none; border-radius:10px 10px 0 0; background:#eef1f7; }
        .mf-tabbar label:hover { background:#e4e9f2; }
        .mf-panels { border:1px solid var(--mf-line); border-top:none; background:#fff; border-radius:0 0 12px 12px; }
        .mf-panel { display:none; }
        .mf-panel iframe { width:100%; height:78vh; border:0; display:block; }
        .mf-panel__msg { padding:28px; line-height:1.6; }
        .mf-panel__msg a { display:inline-block; margin-top:10px; background:var(--mf-accent); color:var(--mf-ink); font-weight:800; text-decoration:none; padding:10px 18px; border-radius:8px; }

        /* Onglets en pur CSS : l'input coché affiche son panneau + active son label */
        #mf-1:checked ~ .mf-panels .mf-p1,
        #mf-2:checked ~ .mf-panels .mf-p2,
        #mf-3:checked ~ .mf-panels .mf-p3,
        #mf-4:checked ~ .mf-panels .mf-p4,
        #mf-5:checked ~ .mf-panels .mf-p5 { display:block; }
        #mf-1:checked ~ .mf-tabbar label[for=mf-1],
        #mf-2:checked ~ .mf-tabbar label[for=mf-2],
        #mf-3:checked ~ .mf-tabbar label[for=mf-3],
        #mf-4:checked ~ .mf-tabbar label[for=mf-4],
        #mf-5:checked ~ .mf-tabbar label[for=mf-5] { background:#fff; color:var(--mf-green); border-color:var(--mf-line); border-bottom:2px solid #fff; margin-bottom:-2px; }
    </style>
</head>
<body>
    <div class="mf-top">
        @if($logo)<img src="{{ $logo }}" alt="Cirkle">@endif
        <h1>{{ $t('Master des formulaires', 'Master of forms') }}</h1>
        <a class="mf-home" href="{{ $u('/') }}">{{ $t('← Retour au site', '← Back to site') }}</a>
    </div>
    <p class="mf-note">{{ $t('Aperçu de tous les formulaires du site, sans inscription. Cliquez sur un onglet.', 'Preview of every form on the site, no registration needed. Click a tab.') }}</p>

    <div class="mf">
        <input class="mf-radio" type="radio" name="mf" id="mf-1" checked>
        <input class="mf-radio" type="radio" name="mf" id="mf-2">
        <input class="mf-radio" type="radio" name="mf" id="mf-3">
        <input class="mf-radio" type="radio" name="mf" id="mf-4">
        <input class="mf-radio" type="radio" name="mf" id="mf-5">

        <nav class="mf-tabbar">
            <label for="mf-1">{{ $t('Accueil', 'Home') }}</label>
            <label for="mf-2">{{ $t('Inscription client', 'Client signup') }}</label>
            <label for="mf-3">{{ $t('Inscription fournisseur', 'Supplier signup') }}</label>
            <label for="mf-4">{{ $t('Connexion', 'Login') }}</label>
            <label for="mf-5">{{ $t('Formulaire 2350', '2350 form') }}</label>
        </nav>

        <div class="mf-panels">
            <section class="mf-panel mf-p1"><iframe src="{{ $u('/') }}" title="Accueil" loading="lazy"></iframe></section>
            <section class="mf-panel mf-p2"><iframe src="{{ $u('/sinscrire') }}" title="Inscription client" loading="lazy"></iframe></section>
            <section class="mf-panel mf-p3"><iframe src="{{ $u('/sinscrire/fournisseur') }}" title="Inscription fournisseur" loading="lazy"></iframe></section>
            <section class="mf-panel mf-p4"><iframe src="{{ $u('/profil') }}" title="Connexion" loading="lazy"></iframe></section>
            <section class="mf-panel mf-p5">
                <div class="mf-panel__msg">
                    <strong>{{ $t('Formulaire 2350', '2350 form') }}</strong><br>
                    {{ $t('Le formulaire 2350 s\'affiche à l\'inscription fournisseur, après le choix d\'une profession. Il devient visible ici dès que vous importez votre première fiche.', 'The 2350 form appears during supplier signup, after choosing a profession. It becomes visible here as soon as you import your first fiche.') }}
                    <br>
                    <a href="{{ url('admin/fiche') }}" target="_blank">{{ $t('Importer une fiche 2350', 'Import a 2350 fiche') }}</a>
                </div>
            </section>
        </div>
    </div>
</body>
</html>

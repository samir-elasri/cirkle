{!! $blocs !!}

{{-- PAGE « MASTER » DE DENIS (demandée les 21, 25 et 30 juin) : « mon master de mon site
     web avec onglets pour chaque form, sans être obligé de m'enregistrer ». Un onglet par
     formulaire/page du site, affiché dans un cadre — aucun mot de passe requis. Les pages
     s'ouvrent aussi dans un nouvel onglet du navigateur. Styles inline : rebuild SCSS gelée. --}}
@php
    $en = app()->getLocale() === 'en';
    $tabs = [
        [$en ? 'Home page' : "Page d'accueil",                       urlRouteName('home')],
        [$en ? 'Client registration' : 'Inscription client',         urlRouteName('register')],
        [$en ? 'Supplier registration (2350)' : 'Inscription fournisseur (2350)', urlRouteName('register-supplier-step-1')],
        [$en ? 'Login / My space' : 'Connexion / Mon espace',        urlRouteName('profile')],
        [$en ? 'Conclusion page' : 'Page Conclusion',                urlRouteName('conclusion')],
        [$en ? 'Ideology / Commitment' : 'Idéologie / Engagement',   urlRouteName('ideologie')],
        [$en ? 'Terms of use' : "Conditions d'utilisation",          urlRouteName('term-of-use')],
        [$en ? 'Privacy policy' : 'Politique de confidentialité',    urlRouteName('privacy-policy')],
        [$en ? 'Cancellation' : 'Résiliation',                       urlRouteName('resiliation')],
    ];
@endphp

<section>
    <div class="optimal-content-width">
        <h2 style="margin-bottom:.3em">{{ $en ? 'MASTER — all the site forms' : 'MASTER — tous les formulaires du site' }}</h2>
        <p style="color:#555;margin-bottom:1em">
            {{ $en
                ? 'One tab per form/page. Everything opens here without registering — you can also open each page in a new browser tab.'
                : "Un onglet par formulaire/page. Tout s'ouvre ici sans inscription — vous pouvez aussi ouvrir chaque page dans un nouvel onglet du navigateur." }}
        </p>

        <style>
            .ck-master-tabs { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px; }
            .ck-master-tabs button {
                border:2px solid #d9d9d9; background:#fff; border-radius:10px; padding:8px 14px;
                font-weight:600; color:#444; cursor:pointer; font-size:.92rem;
            }
            .ck-master-tabs button.is-active { background:#ffd200; border-color:#ffd200; color:#222; }
            .ck-master-open { margin-bottom:8px; font-size:.9rem; }
            .ck-master-frame { width:100%; height:75vh; border:2px solid #d9d9d9; border-radius:10px; background:#fff; }
        </style>

        <div class="ck-master-tabs" id="ck_master_tabs">
            @foreach($tabs as $i => [$label, $url])
                <button type="button" data-url="{{ $url }}" class="{{ $i === 0 ? 'is-active' : '' }}">{{ $label }}</button>
            @endforeach
        </div>

        <div class="ck-master-open">
            <a id="ck_master_open" href="{{ $tabs[0][1] }}" target="_blank">
                🔗 {{ $en ? 'Open this page in a new browser tab' : 'Ouvrir cette page dans un nouvel onglet du navigateur' }}
            </a>
        </div>

        <iframe id="ck_master_frame" class="ck-master-frame" src="{{ $tabs[0][1] }}" title="Master"></iframe>

        <script>
            (function () {
                var tabs = document.getElementById('ck_master_tabs');
                var frame = document.getElementById('ck_master_frame');
                var open = document.getElementById('ck_master_open');
                if (!tabs || !frame) return;
                tabs.addEventListener('click', function (e) {
                    var btn = e.target.closest('button[data-url]');
                    if (!btn) return;
                    tabs.querySelectorAll('button').forEach(function (b) { b.classList.remove('is-active'); });
                    btn.classList.add('is-active');
                    frame.src = btn.getAttribute('data-url');
                    if (open) open.href = btn.getAttribute('data-url');
                });
            })();
        </script>
    </div>
</section>

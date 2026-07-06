{!! $blocs !!}

{{-- Modifier ses codes postaux APRÈS l'inscription (Denis 04.07 : « le fournisseur peut
     changer à sa guise; les changements enregistrés selon la date des facturations »).
     Mêmes règles qu'à l'inscription : 1 à 10 codes, 6 caractères, pas de doublons,
     boîtes sans suggestion du navigateur. Styles inline : rebuild SCSS gelée. --}}
@php
    $en = app()->getLocale() === 'en';
    $codes = $currentCodes ?? [];
@endphp
<section class="ck-auth">
    <div class="optimal-content-width">
        <div class="content-card">
            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">📍 {{ $en ? 'My postal codes' : 'Mes codes postaux' }}</h3>
                    <div class="content-card__label">{{ $en ? 'Change them anytime — 1 to 10 codes.' : 'Modifiables en tout temps — 1 à 10 codes.' }}</div>
                </div>
                <a href="{{ urlRouteName('profile') }}" class="call-to-action" style="white-space:nowrap">← {{ $en ? 'Back to my space' : 'Retour à mon espace' }}</a>
            </div>

            @if(session('error'))
                <div class="form__column"><div style="background:#fdecea;border:2px solid #d93025;border-radius:10px;padding:12px 16px;color:#b00020;font-weight:600">{{ session('error') }}</div></div>
            @endif
            @if(session('success'))
                <div class="form__column"><div style="background:#f2f8f2;border:2px solid #1b9c5a;border-radius:10px;padding:12px 16px;color:#157a47;font-weight:600">{{ session('success') }}</div></div>
            @endif

            {!! Form::open(['url' => urlRouteName('subscriber.profile.updatePostalCodes')]) !!}
                <div class="form__column">
                    <div style="margin-bottom:6px">{{ $en
                        ? 'ENTER 6 CHARACTERS per postal code (ex.: H9P 2T2) — each box must contain a DIFFERENT code, billed per code.'
                        : 'ENTREZ 6 CARACTÈRES par code postal (ex. : H9P 2T2) — chaque boîte doit contenir un code DIFFÉRENT, facturé par code.' }}</div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        @for ($i = 0; $i < 10; $i++)
                            <input style="width:10ch;text-transform:uppercase" class="postal-code-input" type="text" maxlength="7"
                                   name="postal_codes[{{ $i }}]" value="{{ old('postal_codes.'.$i, $codes[$i] ?? '') }}" autocomplete="off"
                                   readonly onfocus="this.removeAttribute('readonly')">
                        @endfor
                    </div>
                </div>

                <div class="form__column">
                    <div style="background:#fff8e1;border:1px solid #ffd200;border-radius:10px;padding:10px 14px;font-size:.9rem">
                        {{ $en
                            ? 'Your changes apply immediately to your visibility. The billing for the NUMBER of codes adjusts on your next renewal invoice.'
                            : 'Vos changements s\'appliquent immédiatement à votre visibilité. La facturation du NOMBRE de codes s\'ajuste à votre prochaine facture de renouvellement.' }}
                    </div>
                </div>

                <div class="content-card__footer">
                    <button type="submit" class="call-to-action">{{ $en ? 'Save my postal codes' : 'Enregistrer mes codes postaux' }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>

<script>
(function () {
    // Doublons vidés en direct (chaque boîte = un code différent).
    var inputs = document.querySelectorAll('.postal-code-input');
    function dedupe() {
        var seen = {};
        inputs.forEach(function (i) {
            var v = i.value.replace(/\s+/g, '').toUpperCase();
            if (v === '') return;
            if (seen[v]) { i.value = ''; }
            else { seen[v] = true; }
        });
    }
    inputs.forEach(function (i) {
        i.addEventListener('input', dedupe);
        i.addEventListener('change', dedupe);
    });
})();
</script>

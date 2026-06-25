{!! $blocs !!}

<section>
    <div class="optimal-content-width">
  
        <div class="content-card">

            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">
                        {{ (isset($isEdit) && $isEdit) ? __('profile.form2350.title') : __('auth.register.title.step2') }}
                    </h3>
                    <div class="content-card__label">
                        {{ (isset($isEdit) && $isEdit) ? __('profile.form2350.intro') : __('auth.register.title.step2-subtitle') }}
                    </div>
                </div>
                @if(!isset($isEdit) || !$isEdit)
                    @include('partials.progress', ['length' => 5, 'active' => 2])
                @endif
            </div>

            {!! Form::open([
                'url' => isset($isEdit) && $isEdit
                    ? urlRouteName('subscriber.profile.updateStep2')
                    : urlRouteName('subscriber.register.storeStep2')
            ]) !!}
                @if (empty($isEdit))
                    <div class="form__column">
                        <div class="registration-title">{{ __('auth.register.provider_type') }}</div>
                        <div class="form__row--error">
                            @foreach($errors->get('provider_type', '<small style="color: red">:message</small>') as $error)
                                {!! $error !!}
                            @endforeach
                        </div>

                        <sl-radio-group name="provider_type" value="{{ old('provider_type') ?? (isset($subscriber) ? $subscriber->provider_type : (session('registerFormData.provider_type') ?? '')) }}">
                            <div class="form__row">
                                <sl-radio value="residential">{{ __('providers.provider-type.residential') }}</sl-radio>
                                <sl-radio value="business">{{ __('providers.provider-type.business') }}</sl-radio>
                            </div>
                        </sl-radio-group>
                    </div>

                    <div class="form__column ">
                        <div class="registration-title">{{ __('auth.register.service_category_id') }}</div>
                        <div class="form__row--error">
                            @foreach($errors->get('service_category_id', '<small style="color: red">:message</small>') as $error)
                                {!! $error !!}
                            @endforeach
                        </div>
                        <sl-select
                            data-component="step2ServiceSelector"
                            data-url="{{ urlRouteName('subscriber.register.step2-service-form') }}"
                            data-accept-url="{{ urlRouteName('subscriber.register.accept-fee') }}"
                            name="service_category_id"
                            id="service_category_id"
                            value="{{ old('service_category_id') ?? (isset($subscriber) ? $subscriber->service_category_id : (session('registerFormData.service_category_id') ?? '')) }}"
                            placeholder="{{ __('main.choose') }}"
                        >
                            @foreach ($subcategories as $category)
                                @if (!$category->title) @continue @endif
                                <sl-option value="{{ $category->id }}">{{ $category->title }}</sl-option>
                            @endforeach
                        </sl-select>
                    </div>
                @endif

                @php
                    // Le 2350 ne s'affiche qu'une fois les frais acceptés (ou en édition).
                    // Le bouton « J'accepte » de la porte (fee-gate) est un submit NON-JS qui
                    // recharge l'étape 2 ; le 2350 est alors rendu côté serveur (aucun <script> requis).
                    $ckCatId = old('service_category_id') ?? (isset($subscriber) ? $subscriber->service_category_id : (session('registerFormData.service_category_id') ?? ''));
                    $ckCat = $ckCatId ? \App\Models\ServiceCategory::find($ckCatId) : null;
                    $ck2350Shown = $ckCat && ((isset($isEdit) && $isEdit) || session("fee_accepted.{$ckCat->id}"));
                @endphp

                <div id="service-container">
                    @if($ckCat)
                        @if($ck2350Shown)
                            @include('partials.service-form', ['serviceCategory' => $ckCat])
                        @else
                            @include('partials.fee-gate', ['serviceCategory' => $ckCat])
                        @endif
                    @endif
                </div>

                {{-- Lien OBLIGATOIRE vers la page CONCLUSION au bas du 2350 (Denis 24.06).
                     Le fournisseur doit l'ouvrir + cocher avant de continuer.
                     N'apparaît qu'avec le 2350 (après acceptation des frais). --}}
                @if((!isset($isEdit) || !$isEdit) && $ck2350Shown)
                    @php $ccLoc = app()->getLocale(); @endphp
                    <div class="form__column" id="conclusion_block" style="margin-top:1.5em;padding:14px 16px;border:2px solid #ffd200;border-radius:10px;background:#fffbe9">
                        <p style="margin:0 0 10px;font-weight:700">
                            {{ $ccLoc === 'en' ? 'Before continuing, please open and read the Conclusion page.' : 'Avant de continuer, veuillez ouvrir et lire la page Conclusion.' }}
                        </p>
                        <a href="{{ urlRouteName('conclusion') }}" target="_blank" class="call-to-action">
                            {{ $ccLoc === 'en' ? 'Open the CONCLUSION page' : 'Ouvrir la page CONCLUSION' }}
                        </a>
                        <div style="margin-top:12px">
                            <sl-checkbox name="conclusion_read" value="1" @if(old('conclusion_read')) checked @endif>
                                {{ $ccLoc === 'en' ? 'I have read the Conclusion page.' : "J'ai lu la page Conclusion." }}
                            </sl-checkbox>
                        </div>
                        <div id="conclusion_error" style="display:none;color:#b00020;font-weight:700;margin-top:10px">
                            {{ $ccLoc === 'en' ? 'Please open the Conclusion page and tick the box before continuing.' : 'Veuillez ouvrir la page Conclusion et cocher la case avant de continuer.' }}
                        </div>
                    </div>
                @endif

                <div class="content-card__footer">
                    @if(isset($isEdit) && $isEdit)
                        <button type="submit" class="call-to-action">
                            {{ __('form.save') }}
                        </button>
                    @else
                        <a href="{{ urlRouteName('register-supplier-step-1') }}" class="call-to-action">{{ __('main.previous') }}</a>
                        @if($ck2350Shown)
                            <button type="submit" class="call-to-action">
                                {{ __('main.next') }}
                            </button>
                        @endif
                    @endif
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>

{{-- « Précisez » verrouillé tant que le « O » du service n'est pas coché (Denis 22.06).
     Écouteur DÉLÉGUÉ sur document : fonctionne même quand le formulaire 2350 est injecté
     par AJAX (innerHTML) — un <script> injecté par innerHTML ne s'exécute pas. --}}
<script>
    document.addEventListener('sl-change', function (e) {
        var box = (e.target && e.target.closest) ? e.target.closest('sl-checkbox') : null;
        if (!box || !box.closest('.form-2350')) return;
        var row = box.closest('.form__row');
        if (!row) return;
        var input = row.querySelector('.supplier-input');
        if (!input) return;
        input.disabled = !box.checked;
        input.classList.toggle('supplier-input--locked', !box.checked);
        if (box.checked) { input.focus(); }
    });

    {{-- Conclusion OBLIGATOIRE : bloque « Suivant » avec un message clair si pas cochée
         (au lieu d'un « required » qui bloquait silencieusement). --}}
    (function () {
        var cc = document.querySelector('sl-checkbox[name="conclusion_read"]');
        if (!cc) return;
        var form = cc.closest('form');
        if (!form) return;
        var err = document.getElementById('conclusion_error');
        form.addEventListener('submit', function (e) {
            if (!cc.checked) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (err) { err.style.display = 'block'; }
                var block = document.getElementById('conclusion_block');
                (block || cc).scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, true);
        cc.addEventListener('sl-change', function () {
            if (cc.checked && err) { err.style.display = 'none'; }
        });
    })();

    /* Frais UNIQUE selon la plateforme : le montant affiché dans la porte d'acceptation
       suit le bouton « choix de la plateforme » (résidentiel 75 $ / B2B 100 $). Aucun
       Blade ici — les deux montants viennent des data-* de .fee-gate__amount. */
    (function () {
        function currentType() {
            var g = document.querySelector('sl-radio-group[name="provider_type"]');
            return (g && g.value === 'business') ? 'business' : 'residential';
        }
        function applyFee() {
            var amt = document.querySelector('.fee-gate__amount');
            if (!amt) return;
            var v = amt.getAttribute('data-fee-' + currentType());
            // garde anti-boucle : ne réécrit que si la valeur change réellement.
            if (v && amt.textContent !== v) { amt.textContent = v; }
        }
        document.addEventListener('sl-change', function (e) {
            var t = e.target;
            if (t && t.getAttribute && t.getAttribute('name') === 'provider_type') { applyFee(); }
        });
        // On observe UNIQUEMENT les enfants directs de #service-container (l'injection AJAX
        // de la porte). PAS de subtree : sinon applyFee() qui modifie .fee-gate__amount
        // (descendant) re-déclenchait l'observateur → boucle infinie qui gelait la page.
        var container = document.getElementById('service-container');
        if (container && window.MutationObserver) {
            new MutationObserver(applyFee).observe(container, { childList: true });
        }
        applyFee();
    })();
</script>
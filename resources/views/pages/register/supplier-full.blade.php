{!! $blocs !!}

{{-- INSCRIPTION FOURNISSEUR — UNE SEULE PAGE (Denis 28.06 : « pas plusieurs fenêtres,
     tous les form en un seul endroit, avec un bouton retour, minimiser les espaces »).
     Round 1 : mise en page à valider. L'assistant 6 étapes reste la voie active; le
     bouton « S'enregistrer » affiche un message tant que le handler combiné (round 2)
     n'est pas câblé. --}}
<section class="ck-auth ck-auth--wide">
    <div class="optimal-content-width">
        <div class="content-card">

            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">{{ __('auth.register.title.step1') }}</h3>
                    <div class="content-card__label">{{ app()->getLocale() === 'en' ? 'Everything on one page.' : 'Tout sur une seule page.' }}</div>
                </div>
                <a href="{{ urlRouteName('home') }}" class="call-to-action" style="white-space:nowrap">← {{ app()->getLocale() === 'en' ? 'Back to site' : 'Retour au site' }}</a>
            </div>

            @if(session('error'))
                <div class="form__column"><div class="ui info message" style="background:#fff8e1;border:1px solid #ffd200;border-radius:10px;padding:12px 16px">{{ session('error') }}</div></div>
            @endif

            {!! Form::open(['url' => urlRouteName('subscriber.register.storeSupplierFull')]) !!}
                <input type="hidden" name="preference_language" value="{{ App::getLocale() }}">

                {{-- ───────────── 1) VOS COORDONNÉES ───────────── --}}
                <div class="registration-title">1. {{ app()->getLocale() === 'en' ? 'Your details' : 'Vos coordonnées' }}</div>

                <div class="form__column"><input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="{{ __('auth.register.company_name') }}"></div>
                <div class="form__column"><input type="text" name="owner_names" value="{{ old('owner_names') }}" placeholder="{{ __('auth.register.owner_names') }}"></div>
                <div class="form__column">
                    <sl-select name="legal_form_id" style="width:100%;display:block" value="{{ old('legal_form_id') }}" placeholder="{{ __('auth.register.legal_form_id') }}">
                        @foreach ($legalForms as $lf)<sl-option value="{{ $lf->id }}">{{ $lf->title }}</sl-option>@endforeach
                    </sl-select>
                </div>
                <div class="form__column"><input type="text" name="federal_tax_number" value="{{ old('federal_tax_number') }}" placeholder="{{ __('auth.register.federal_tax_number') }}"></div>
                <div class="form__column"><input type="text" name="street" value="{{ old('street') }}" placeholder="{{ __('auth.register.street') }}"></div>
                <div class="form__column"><input type="text" name="city" value="{{ old('city') }}" placeholder="{{ __('auth.register.city') }}"></div>
                <div class="form__column"><input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="{{ __('auth.register.postal_code') }}"></div>
                <div class="form__column"><input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('auth.register.phone') }}"></div>
                <div class="form__column"><input type="text" name="toll_free_phone" value="{{ old('toll_free_phone') }}" placeholder="{{ __('auth.register.toll_free_phone') }}"></div>
                <div class="form__column"><input type="text" name="fax" value="{{ old('fax') }}" placeholder="{{ __('auth.register.fax') }}"></div>
                <div class="form__column"><input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('auth.register.email') }}"></div>
                <div class="form__column"><label for="start_date">{{ __('auth.register.start_date') }}</label><input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"></div>
                <div class="form__column"><input type="text" name="business_hours" value="{{ old('business_hours') }}" placeholder="{{ __('auth.register.business_hours') }}"></div>
                <div class="form__column"><input type="text" name="insurance_coverage" value="{{ old('insurance_coverage') }}" placeholder="{{ __('auth.register.insurance_coverage') }}"></div>

                {{-- ───────────── 2) VOTRE 2350 ───────────── --}}
                <div class="registration-title">2. {{ app()->getLocale() === 'en' ? 'Your 2350' : 'Votre 2350' }}</div>

                <div class="form__column">
                    <div class="registration-title" style="font-size:1rem !important;border:none !important;margin:0 0 6px">{{ __('auth.register.provider_type') }}</div>
                    <sl-radio-group name="provider_type" value="{{ old('provider_type') }}">
                        <div class="form__row">
                            <sl-radio value="residential">{{ __('providers.provider-type.residential') }}</sl-radio>
                            <sl-radio value="business">{{ __('providers.provider-type.business') }}</sl-radio>
                        </div>
                    </sl-radio-group>
                </div>

                <div class="form__column">
                    <div class="registration-title" style="font-size:1rem !important;border:none !important;margin:0 0 6px">{{ __('auth.register.service_category_id') }}</div>
                    <sl-select
                        data-component="step2ServiceSelector"
                        data-url="{{ urlRouteName('subscriber.register.step2-service-form-inline') }}"
                        name="service_category_id" id="service_category_id"
                        value="{{ old('service_category_id') }}" placeholder="{{ __('main.choose') }}">
                        @foreach ($subcategories as $category)
                            @if (!$category->title) @continue @endif
                            <sl-option value="{{ $category->id }}">{{ $category->title }}</sl-option>
                        @endforeach
                    </sl-select>
                </div>
                <div class="form__column">
                    <div class="ui info message" style="background:#fff9e6;border:1px solid #e6b800;border-radius:10px;padding:10px 14px;font-size:.92rem">
                        💡 {{ app()->getLocale() === 'en'
                            ? 'A one-time competence-sheet fee applies (residential $75 / B2B $100), added at payment.'
                            : 'Des frais uniques de fiche s\'appliquent (résidentiel 75 $ / B2B 100 $), ajoutés au paiement.' }}
                    </div>
                    <div style="margin-top:8px">
                        <sl-checkbox name="accept_fee" value="1" @if(old('accept_fee')) checked @endif>
                            {{ app()->getLocale() === 'en' ? 'I accept these competence-sheet fees.' : "J'accepte ces frais de fiche." }}
                        </sl-checkbox>
                    </div>
                    <div class="form__row--error">
                        @foreach($errors->get('accept_fee', '<small style="color: red">:message</small>') as $error){!! $error !!}@endforeach
                    </div>
                </div>
                <div id="service-container"></div>

                {{-- ───────────── 3) ZONE & FORFAIT ───────────── --}}
                <div class="registration-title">3. {{ app()->getLocale() === 'en' ? 'Area & plan' : 'Zone & forfait' }}</div>

                <div class="form__column">
                    <label for="subscription_id">{{ __('auth.register.subscription_id') }}</label>
                    <sl-select name="subscription_id" id="subscription_id" value="{{ old('subscription_id') }}">
                        @foreach($subscriptions as $subscription)
                            <sl-option value="{{ $subscription->id }}" data-max-postal-codes="{{ $subscription->max_postal_codes }}">{{ $subscription->title }}</sl-option>
                        @endforeach
                    </sl-select>
                </div>

                <style>
                    .zone-toggle { display:inline-flex; border:2px solid #d9d9d9; border-radius:10px; overflow:hidden; margin-top:4px; }
                    .zone-toggle .zone-opt { display:flex; align-items:center; gap:8px; padding:10px 18px; cursor:pointer; font-weight:600; color:#555; background:#fff; user-select:none; }
                    .zone-toggle .zone-opt + .zone-opt { border-left:2px solid #d9d9d9; }
                    .zone-toggle .zone-opt input { position:absolute; opacity:0; pointer-events:none; }
                    .zone-toggle .zone-opt.is-active { background:#ffd200; color:#222; }
                </style>
                <div class="form__column">
                    <label style="font-weight:700">{{ app()->getLocale() === 'en' ? 'Service area' : 'Zone desservie' }}</label>
                    <div class="zone-toggle">
                        <label class="zone-opt is-active"><input type="radio" name="zone_type" value="postal" checked><span>📍 {{ app()->getLocale() === 'en' ? 'By postal code' : 'Par code postal' }}</span></label>
                        @if($provinces->isNotEmpty())
                            <label class="zone-opt"><input type="radio" name="zone_type" value="province"><span>🍁 {{ app()->getLocale() === 'en' ? 'By province' : 'Par province' }}</span></label>
                        @endif
                    </div>
                </div>

                <div class="form__column" id="postal_block">
                    <label for="postal_codes">{{ __('auth.register.postal_codes') }}</label>
                    <div>{{ app()->getLocale() === 'en' ? 'The plan includes' : 'Le forfait inclut' }} <span id="max_postal_codes">—</span> {{ app()->getLocale() === 'en' ? 'postal codes.' : 'codes postaux.' }}</div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        @for ($i = 0; $i < ($subscriptions->max('max_postal_codes') ?: 10); $i++)
                            <input style="width:7ch" class="postal-code-input" data-i="{{$i}}" type="text" name="postal_codes[{{ $i }}]" value="{{ old('postal_codes.'.$i) }}">
                        @endfor
                    </div>
                </div>

                @if($provinces->isNotEmpty())
                    <div class="form__column" id="province_block" style="display:none">
                        <label for="subscription_state_id">Province</label>
                        <sl-select name="subscription_state_id" id="subscription_state_id" value="{{ old('subscription_state_id') }}">
                            @foreach($provinces as $province)<sl-option value="{{ $province->id }}">{{ $province->title }}</sl-option>@endforeach
                        </sl-select>
                    </div>
                @endif

                <div class="form__column">
                    <div class="ui segment" style="display:flex;justify-content:space-between;align-items:center;border:1px solid #e2e2e2;border-radius:10px;padding:12px 16px">
                        <strong>{{ app()->getLocale() === 'en' ? 'Plan price' : 'Prix du forfait' }}</strong><strong id="forfait_price_display">—</strong>
                    </div>
                </div>

                {{-- ───────────── 4) OPTIONS ───────────── --}}
                <div class="registration-title">4. {{ app()->getLocale() === 'en' ? 'Options' : 'Options' }}</div>
                <div class="form__column">
                    @foreach($profileOptions as $option)
                        @if($option === 'url') @continue @endif
                        <div style="padding:3px 0"><sl-checkbox name="{{ $option }}" value="1" @if(old($option)) checked @endif>{{ setting("{$option}_title", $option) }}</sl-checkbox></div>
                    @endforeach
                    <div style="font-size:.85rem;color:#777;margin-top:6px">{{ app()->getLocale() === 'en' ? 'Details (and the website option) can be added from your profile after signup.' : 'Les détails (et l\'option site web) s\'ajoutent depuis votre profil après l\'inscription.' }}</div>
                </div>

                {{-- ───────────── 5) MOT DE PASSE & CONDITIONS ───────────── --}}
                <div class="registration-title">5. {{ app()->getLocale() === 'en' ? 'Password & terms' : 'Mot de passe & conditions' }}</div>
                <div class="form__column"><input type="password" name="password" placeholder="{{ __('auth.register.password') }}"></div>
                <div class="form__column"><input type="password" name="password_confirmation" placeholder="{{ __('auth.register.password_confirmation') }}"></div>
                <div class="form__column">
                    <sl-checkbox name="accept_condition" value="1"><a href="{!! urlRouteName('term-of-use') !!}" target="_blank">{{ __('auth.register.terms') }}</a></sl-checkbox>
                </div>

                <div class="content-card__footer">
                    <a href="{{ urlRouteName('home') }}" class="call-to-action">← {{ app()->getLocale() === 'en' ? 'Back to site' : 'Retour au site' }}</a>
                    <button type="submit" class="call-to-action">{{ __('main.submit') }}</button>
                </div>
            {!! Form::close() !!}

        </div>
    </div>
</section>

@include('partials.password-eye')

{{-- Prix du forfait calculé côté client : priceMap[catégorie][abonnement][zone]. --}}
<script>
(function () {
    var priceMap = {!! json_encode($priceMap, JSON_UNESCAPED_UNICODE) !!};
    var cat  = document.getElementById('service_category_id');
    var sub  = document.getElementById('subscription_id');
    var prov = document.getElementById('subscription_state_id');
    var postalBlock = document.getElementById('postal_block');
    var provBlock   = document.getElementById('province_block');
    var priceEl = document.getElementById('forfait_price_display');
    var maxEl   = document.getElementById('max_postal_codes');
    var postalInputs = document.querySelectorAll('.postal-code-input');
    var fmt = new Intl.NumberFormat('fr-CA', { style:'currency', currency:'CAD' });
    function val(el){ return el ? (el.value || '') : ''; }
    function zone(){ var r = document.querySelector('input[name="zone_type"]:checked'); return r ? r.value : 'postal'; }
    function update(){
        var z = zone();
        document.querySelectorAll('.zone-toggle .zone-opt').forEach(function(l){ var i=l.querySelector('input'); l.classList.toggle('is-active', !!(i&&i.checked)); });
        if (postalBlock) postalBlock.style.display = (z==='province') ? 'none' : '';
        if (provBlock)   provBlock.style.display   = (z==='province') ? '' : 'none';
        var opt = sub ? sub.querySelector('sl-option[value="'+val(sub)+'"]') : null;
        var maxPC = opt ? parseInt(opt.getAttribute('data-max-postal-codes')||'0',10) : 0;
        if (maxEl) maxEl.textContent = maxPC || '—';
        postalInputs.forEach(function(input){ input.style.display = (parseInt(input.dataset.i,10) >= maxPC) ? 'none' : ''; });
        var map = ((priceMap[val(cat)]||{})[val(sub)]) || {};
        var cost = (z==='province') ? map[val(prov)] : map['postal'];
        priceEl.textContent = (cost || cost===0) ? fmt.format(cost) : '—';
    }
    document.querySelectorAll('input[name="zone_type"]').forEach(function(r){ r.addEventListener('change', update); });
    [cat, sub, prov].forEach(function(el){ if (el) el.addEventListener('sl-change', update); });
    if (window.customElements && customElements.whenDefined) { customElements.whenDefined('sl-select').then(function(){ setTimeout(update,0); }); }
    setTimeout(update, 0);
})();
</script>

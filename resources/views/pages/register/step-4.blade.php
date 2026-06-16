{!! $blocs !!}

<section>
    <div class="optimal-content-width">

        <div class="content-card">
            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">Enregistrement du fournisseur</h3>
                    <div class="content-card__label">Enregistrement du fournisseur</div>
                </div>
                @include('partials.progress', ['length' => 5, 'active' => 4])
            </div>

            {!! Form::open(['url' => urlRouteName('subscriber.register.storeStep4')]) !!}

                {{-- 1) Durée du forfait --}}
                <div class="form__column ">
                    <div class="form__row--error">
                        <label for="subscription_id">{{ __('auth.register.subscription_id') }}</label>
                        @foreach($errors->get('subscription_id', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <sl-select name="subscription_id" id="subscription_id" value="{{ old('subscription_id') ?? session('registerFormData.subscription_id') ?? '' }}">
                        @foreach($subscriptions as $subscription)
                            <sl-option value="{{ $subscription->id }}" data-max-postal-codes="{{ $subscription->max_postal_codes }}">
                                {{ $subscription->title }}
                            </sl-option>
                        @endforeach
                    </sl-select>
                </div>

                {{-- 2) Zone desservie : par code postal OU par province (cahier de charges) --}}
                @php $zoneOld = old('zone_type') ?? session('registerFormData.zone_type') ?? 'postal'; @endphp
                <style>
                    .zone-toggle { display:inline-flex; border:2px solid #d9d9d9; border-radius:10px; overflow:hidden; margin-top:8px; }
                    .zone-toggle .zone-opt { position:relative; display:flex; align-items:center; gap:8px; padding:12px 22px; cursor:pointer; font-weight:600; color:#555; background:#fff; user-select:none; transition:background .12s,color .12s; }
                    .zone-toggle .zone-opt + .zone-opt { border-left:2px solid #d9d9d9; }
                    .zone-toggle .zone-opt input { position:absolute; opacity:0; pointer-events:none; }
                    .zone-toggle .zone-opt:hover { background:#fafafa; }
                    .zone-toggle .zone-opt.is-active { background:#ffd200; color:#222; }
                    .zone-toggle .zone-opt:has(input:checked) { background:#ffd200; color:#222; }
                </style>
                <div class="form__column">
                    <label style="font-weight:700;">Zone desservie</label>
                    <div class="zone-toggle">
                        <label class="zone-opt {{ $zoneOld !== 'province' ? 'is-active' : '' }}">
                            <input type="radio" name="zone_type" value="postal" {{ $zoneOld !== 'province' ? 'checked' : '' }}>
                            <span>📍 Par code postal</span>
                        </label>
                        @if($provinces->isNotEmpty())
                            <label class="zone-opt {{ $zoneOld === 'province' ? 'is-active' : '' }}">
                                <input type="radio" name="zone_type" value="province" {{ $zoneOld === 'province' ? 'checked' : '' }}>
                                <span>🍁 Par province</span>
                            </label>
                        @endif
                    </div>
                </div>

                {{-- 2a) Codes postaux (1 à N selon le forfait) --}}
                <div class="form__column" id="postal_block">
                    <label for="postal_codes">{{ __('auth.register.postal_codes') }}</label>
                    @foreach($errors->get('postal_codes', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <div>Le forfait inclut <span id="max_postal_codes"></span> codes postaux.</div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap">
                        @for ($i = 0; $i < $subscriptions->max('max_postal_codes'); $i++)
                            <input style="width:7ch" class="postal-code-input" data-i="{{$i}}" type="text" name="postal_codes[{{ $i }}]" value="{{ old('postal_codes.'.$i) ?? session('registerFormData.postal_codes.'.$i) ?? '' }}">
                        @endfor
                    </div>
                </div>

                {{-- 2b) Province --}}
                @if($provinces->isNotEmpty())
                    <div class="form__column" id="province_block" style="display:none">
                        <div class="form__row--error">
                            <label for="subscription_state_id">Province</label>
                            @foreach($errors->get('subscription_state_id', '<small style="color: red">:message</small>') as $error)
                                {!! $error !!}
                            @endforeach
                        </div>
                        <sl-select name="subscription_state_id" id="subscription_state_id" value="{{ old('subscription_state_id') ?? session('registerFormData.subscription_state_id') ?? '' }}">
                            @foreach($provinces as $province)
                                <sl-option value="{{ $province->id }}">{{ $province->title }}</sl-option>
                            @endforeach
                        </sl-select>
                    </div>
                @endif

                {{-- 3) Prix du forfait sélectionné --}}
                <div class="form__column">
                    <div class="ui segment" style="display:flex;justify-content:space-between;align-items:center">
                        <strong>Prix du forfait</strong>
                        <strong id="forfait_price_display">—</strong>
                    </div>
                </div>

                @if(setting('registration_fee') > 0)
                <div class="form__column">
                    <div class="ui info message">
                        <i class="info circle icon"></i>
                        Note: {{ __('auth.register.registration_fee_note', ['amount' => prettyPrice(setting('registration_fee'))]) }}
                    </div>
                </div>
                @endif

                <div class="content-card__footer">
                    <a href="{{ urlRouteName('register-supplier-step-3') }}" class="call-to-action">{{ __('main.previous') }}</a>

                    <button type="submit" class="call-to-action">
                        {{ __('main.next') }}
                    </button>
                </div>

            {!! Form::close() !!}

            {{-- Forfait par code postal OU par province : calcul du prix côté client (sans rebuild JS) --}}
            <script>
            (function () {
                var priceMap = @json($priceMap, JSON_UNESCAPED_UNICODE);
                var sub      = document.getElementById('subscription_id');
                var prov     = document.getElementById('subscription_state_id');
                var postalBlock = document.getElementById('postal_block');
                var provBlock   = document.getElementById('province_block');
                var priceEl  = document.getElementById('forfait_price_display');
                var maxEl    = document.getElementById('max_postal_codes');
                var postalInputs = document.querySelectorAll('.postal-code-input');
                var fmt = new Intl.NumberFormat('fr-CA', { style: 'currency', currency: 'CAD' });

                function zone() {
                    var r = document.querySelector('input[name="zone_type"]:checked');
                    return r ? r.value : 'postal';
                }
                function value(el) { return el ? (el.value || '') : ''; }

                function update() {
                    var z = zone();
                    document.querySelectorAll('.zone-toggle .zone-opt').forEach(function (l) {
                        var i = l.querySelector('input');
                        l.classList.toggle('is-active', !!(i && i.checked));
                    });
                    if (postalBlock) postalBlock.style.display = (z === 'province') ? 'none' : '';
                    if (provBlock)   provBlock.style.display   = (z === 'province') ? '' : 'none';

                    // Nombre de codes postaux inclus selon la durée choisie
                    var subId = value(sub);
                    var opt = sub ? sub.querySelector('sl-option[value="' + subId + '"]') : null;
                    var maxPC = opt ? parseInt(opt.getAttribute('data-max-postal-codes') || '0', 10) : 0;
                    if (maxEl) maxEl.textContent = maxPC;
                    postalInputs.forEach(function (input) {
                        input.classList.toggle('hide', parseInt(input.dataset.i, 10) >= maxPC);
                    });

                    // Prix selon la zone
                    var map = priceMap[subId] || {};
                    var cost = (z === 'province') ? map[value(prov)] : map['postal'];
                    priceEl.textContent = (cost || cost === 0) ? fmt.format(cost) : '—';
                }

                document.querySelectorAll('input[name="zone_type"]').forEach(function (r) {
                    r.addEventListener('change', update);
                });
                if (sub)  sub.addEventListener('sl-change', update);
                if (prov) prov.addEventListener('sl-change', update);
                if (window.customElements && customElements.whenDefined) {
                    customElements.whenDefined('sl-select').then(function () { setTimeout(update, 0); });
                }
                setTimeout(update, 0);
            })();
            </script>
        </div>
    </div>
</section>

{!! $blocs !!}

<section>
    <div class="optimal-content-width">
        <div class="content-card">
            <div class="content-card__header">
                <div>
                    @if(isset($isEdit) && $isEdit)
                        <h3 class="content-card__header--title">Modifier les options de profil</h3>
                        <div class="content-card__label">Modifiez vos options de profil actives</div>
                    @else
                        <h3 class="content-card__header--title">Enregistrement du fournisseur</h3>
                        <div class="content-card__label">Enregistrement du fournisseur</div>
                    @endif
                </div>
                @if(!isset($isEdit) || !$isEdit)
                    @include('partials.progress', ['length' => 5, 'active' => 5])
                @endif
            </div>

            {!! Form::open(['up-submit' => 'false', 'url' => isset($isEdit) && $isEdit ? urlRouteName('subscriber.profile.updateStep5') : urlRouteName('subscriber.register.storeStep5')]) !!}

                {{-- <div>
                    <img src="/dist/img/info.svg" alt="">
                    <a href="{{ urlRouteName('options-list') }}" target="_blank">Options</a>
                </div> --}}

                @foreach($profileOptions as $option)
                    @if(isset($isEdit) && $isEdit)
                        {{-- In edit mode, show options directly without hide class --}}
                        <div id="{{$option}}_details">
                            <details class="details" open>
                                <summary>{{setting("{$option}_title", "{$option}_title")}}</summary>
                                <div>
                                    @if ($option === 'license')
                                        @include('pages.profile-options.licenses', ['data' => $subscriber->licenses ?? []])
                                    @elseif ($option === 'diploma')
                                        @include('pages.profile-options.diplomas', ['data' => $subscriber->diplomas ?? []])
                                    @elseif ($option === 'promotion')
                                        @include('pages.profile-options.promotions', ['data' => $subscriber->promotions ?? []])
                                    @elseif ($option === 'image')
                                        @include('pages.profile-options.photos', ['data' => $subscriber->subscriberImages ?? []])
                                    @elseif ($option === 'estimation')
                                        @include('pages.profile-options.estimations', [
                                            'data' => $subscriber,
                                            'registrationForm' => !$isEdit,
                                        ])
                                    @elseif ($option === 'job_offer')
                                        @include('pages.profile-options.joboffers', ['data' => $subscriber->jobOffers ?? []])
                                    @elseif ($option === 'url')
                                        @include('pages.profile-options.url', [
                                            'data' => $subscriber,
                                            'registrationForm' => !$isEdit,
                                        ])
                                    @endif
                                </div>
                            </details>
                        </div>
                    @else
                        {{-- Registration mode - original behavior --}}
                        <div id="{{$option}}_details" class="hide">
                            <details class="details">
                                <summary>{{setting("{$option}_title", "{$option}_title")}}</summary>
                                <div>
                                    @if ($option === 'license')
                                        @include('pages.profile-options.licenses', ['data' => []])
                                    @elseif ($option === 'diploma')
                                        @include('pages.profile-options.diplomas', ['data' => []])
                                    @elseif ($option === 'promotion')
                                        @include('pages.profile-options.promotions', ['data' => []])
                                    @elseif ($option === 'image')
                                        @include('pages.profile-options.photos', ['data' => []])
                                    @elseif ($option === 'estimation')
                                        @include('pages.profile-options.estimations', [
                                            'data' => $subscriber,
                                            'registrationForm' => true,
                                        ])
                                    @elseif ($option === 'job_offer')
                                        @include('pages.profile-options.joboffers', ['data' => []])
                                    @elseif ($option === 'url')
                                        @include('pages.profile-options.url', [
                                            'data' => $subscriber,
                                            'registrationForm' => true,
                                        ])
                                    @endif

                                    {{-- Retrait clair d'une option ajoutée (sans devoir rouvrir le menu) --}}
                                    <div style="margin-top:14px;">
                                        <button type="button" class="option-remove" data-option="{{ $option }}"
                                            style="background:#fff;border:1px solid #d33;color:#d33;border-radius:6px;padding:7px 15px;cursor:pointer;font-weight:600;">
                                            ✕ Retirer cette option
                                        </button>
                                    </div>
                                </div>
                            </details>
                        </div>
                    @endif
                @endforeach

                @if(!isset($isEdit) || !$isEdit)
                    {{-- Only show dropdown in registration mode --}}
                    <sl-dropdown>
                        <button class="call-to-action" type="button" slot="trigger">+ Ajouter une option</button>
                        <div class="sl-dropdown-content">
                            @foreach($profileOptions as $option)
                                <div>
                                    <sl-checkbox
                                        data-component="step5Toggler"
                                        data-target="#{{ $option }}_details"
                                        name="{{ $option }}"
                                        @if(!empty(old($option)) || !empty(session('registerFormData.'.$option))) checked @endif
                                    >
                                        <h3 style="margin:0">{{setting("{$option}_title", "{$option}_title")}}@if($option === 'promotion')<span class="ck-opt-promo" title="Logo à côté de votre nom">PROMO</span>@elseif($option === 'job_offer')<img class="ck-opt-e" src="{{ asset_with_version('/dist/img/cirkle-e-badge.png') }}" alt="" title="Logo à côté de votre nom">@endif</h3>
                                    </sl-checkbox>
                                </div>
                                <p>
                                    {{setting("{$option}_description", "{$option}_description")}}
                                </p>
                                {{-- {{ prettyPrice(setting("{$option}_price")) }} --}}
                            @endforeach
                        </div>
                    </sl-dropdown>
                    
                    {{-- Sommaire des frais : se recalcule quand on ajoute/retire une option --}}
                    @php
                        $optionPrices = [
                            'license'    => (float) setting('license_price'),
                            'diploma'    => (float) setting('diploma_price'),
                            'promotion'  => (float) setting('promotion_price'),
                            'image'      => (float) setting('image_price'),
                            'estimation' => (float) setting('estimation_price'),
                            'job_offer'  => (float) setting('job_offer_price'),
                        ];
                        $optionTitles = [];
                        foreach (array_keys($optionPrices) as $o) { $optionTitles[$o] = setting("{$o}_title", $o); }
                        $optionTitles['url'] = setting('url_title', 'Site web');
                    @endphp
                    <div class="form__column" style="margin-top: 20px;">
                        <div class="ui segment" style="border:1px solid #e2e2e2;border-radius:10px;padding:16px;">
                            <h4 style="margin:0 0 10px;">Sommaire des frais</h4>
                            <div id="fees-lines"></div>
                            <div style="display:flex;justify-content:space-between;border-top:2px solid #222;padding-top:10px;margin-top:6px;font-size:1.08em;">
                                <strong>Total</strong><strong id="fees-total">—</strong>
                            </div>
                            <div style="border-top:1px solid #eee;padding-top:8px;margin-top:8px;font-size:.85em;color:#666;">
                                <i class="info circle icon"></i>
                                Le prix de l'abonnement choisi s'ajoute à ces frais au moment du paiement.
                            </div>
                        </div>
                    </div>

                    <script>
                    (function () {
                        var registrationFee  = {{ (float) (setting('registration_fee') ?? 0) }};
                        var registrationLabel = @json(setting('registration_fee_title') ?: "Frais d'inscription");
                        var optionPrices = @json($optionPrices, JSON_UNESCAPED_UNICODE);
                        var optionTitles = @json($optionTitles, JSON_UNESCAPED_UNICODE);
                        var websiteTiers = @json(\App\Support\WebsiteForfait::tiers());
                        var fmt = new Intl.NumberFormat('fr-CA', { style:'currency', currency:'CAD' });
                        var linesEl = document.getElementById('fees-lines');
                        var totalEl = document.getElementById('fees-total');

                        function optionChecked(name) {
                            var cb = document.querySelector('sl-checkbox[name="' + name + '"]');
                            return cb ? !!cb.checked : false;
                        }
                        function websitePrice() {
                            var sel = document.getElementById('url_forfait');
                            if (!sel || !sel.value) return 0;
                            var p = sel.value.split('-'), t = p[0], m = p[1];
                            return (websiteTiers[t] && websiteTiers[t][m]) ? Number(websiteTiers[t][m]) : 0;
                        }
                        function recompute() {
                            var lines = [], total = 0;
                            if (registrationFee > 0) { lines.push([registrationLabel, registrationFee]); total += registrationFee; }
                            Object.keys(optionPrices).forEach(function (o) {
                                if (optionChecked(o)) { var pr = Number(optionPrices[o]) || 0; lines.push([optionTitles[o] || o, pr]); total += pr; }
                            });
                            if (optionChecked('url')) { var wp = websitePrice(); lines.push([optionTitles['url'] || 'Site web', wp]); total += wp; }
                            linesEl.innerHTML = lines.length ? lines.map(function (l) {
                                return '<div style="display:flex;justify-content:space-between;padding:5px 0;"><span>' +
                                    l[0] + '</span><span>' + fmt.format(l[1]) + '</span></div>';
                            }).join('') : '<div style="color:#888;padding:5px 0;">Aucune option sélectionnée.</div>';
                            totalEl.textContent = fmt.format(total);
                        }

                        document.querySelectorAll('sl-checkbox[data-component="step5Toggler"]').forEach(function (cb) {
                            cb.addEventListener('sl-change', recompute);
                        });
                        document.addEventListener('change', function (e) {
                            if (e.target && e.target.id === 'url_forfait') recompute();
                        });
                        document.addEventListener('click', function (e) {
                            var btn = e.target.closest ? e.target.closest('.option-remove') : null;
                            if (!btn) return;
                            e.preventDefault();
                            var name = btn.dataset.option;
                            var cb = document.querySelector('sl-checkbox[name="' + name + '"]');
                            if (cb) { cb.checked = false; cb.dispatchEvent(new CustomEvent('sl-change', { bubbles:true })); }
                            var details = document.getElementById(name + '_details');
                            if (details) details.classList.add('hide'); // repli si le toggler ne réagit pas
                            recompute();
                        });
                        if (window.customElements && customElements.whenDefined) {
                            customElements.whenDefined('sl-checkbox').then(function () { setTimeout(recompute, 0); });
                        }
                        setTimeout(recompute, 0);
                    })();
                    </script>
                @endif

                <div class="content-card__footer">
                    @if(!isset($isEdit) || !$isEdit)
                        <a href="{{ urlRouteName('register-supplier-step-4') }}" class="call-to-action">{{ __('main.previous') }}</a>
                    @endif

                    <button type="submit" class="call-to-action">
                        @if(isset($isEdit) && $isEdit)
                            {{ __('form.save') }}
                        @else
                            {{ __('main.submit') }}
                        @endif
                    </button>
                </div>

            {!! Form::close() !!}
        </div>
    </div>
</section>

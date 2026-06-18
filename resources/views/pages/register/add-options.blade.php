{!! $blocs !!}

<section>
    <div class="optimal-content-width">
        <div class="content-card">
            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">@lang('profile.add-options.title')</h3>
                    <div class="content-card__label">@lang('profile.add-options.intro')</div>
                </div>
            </div>

            @if(empty($availableOptions))
                <p class="add-options-none">@lang('profile.add-options.all-active')</p>
                <div class="content-card__footer">
                    <a href="{{ urlRouteName('profile') }}" class="call-to-action cta-alt">@lang('profile.add-options.back')</a>
                </div>
            @else
                @php
                    $optionPrices = [
                        'license'    => (float) setting('license_price'),
                        'diploma'    => (float) setting('diploma_price'),
                        'promotion'  => (float) setting('promotion_price'),
                        'image'      => (float) setting('image_price'),
                        'estimation' => (float) setting('estimation_price'),
                        'job_offer'  => (float) setting('job_offer_price'),
                    ];
                    $optionLabels = [];
                    foreach (['license','diploma','promotion','image','estimation','job_offer','url'] as $o) {
                        $optionLabels[$o] = __('profile.add-options.option.'.$o);
                    }
                    $tiers = \App\Support\WebsiteForfait::tiers();
                @endphp

                {!! Form::open(['url' => urlRouteName('subscriber.profile.add-options.store'), 'data-ref' => 'add-options-form']) !!}

                    <div class="add-options-grid">
                        @foreach($availableOptions as $option)
                            <label class="add-option-card" for="opt_{{ $option }}">
                                <input type="checkbox" name="options[]" id="opt_{{ $option }}" value="{{ $option }}" class="add-option-card__cb">
                                <span class="add-option-card__check" aria-hidden="true">✓</span>
                                <span class="add-option-card__title">@lang('profile.add-options.option.'.$option)</span>
                                <span class="add-option-card__desc">@lang('profile.add-options.desc.'.$option)</span>
                                <span class="add-option-card__price">
                                    @if($option === 'url')
                                        @lang('profile.add-options.from') {{ prettyPrice(100) }}
                                    @else
                                        {{ prettyPrice($optionPrices[$option]) }}
                                    @endif
                                </span>
                                {{-- Bouton « Ouvrir » : ouvre/ferme le sous-formulaire de l'option (demandé par Denis).
                                     <button> dans <label> = élément interactif, ne coche pas la case. --}}
                                <button type="button" class="add-option-card__open"
                                        onclick="var s=document.getElementById('subform_{{ $option }}'); s.style.display=(s.style.display==='block')?'none':'block';">
                                    ▸ @lang('profile.add-options.open')
                                </button>
                            </label>
                        @endforeach
                    </div>

                    {{-- Sous-formulaire de chaque option, révélé quand la case est cochée.
                         Les listes (permis/diplômes/photos/etc.) s'enregistrent directement
                         (AJAX) sur le compte; les champs estimation/site web sont soumis avec
                         le formulaire. L'option est facturée au panier puis activée au paiement. --}}
                    @foreach($availableOptions as $option)
                        <div class="add-option-subform" id="subform_{{ $option }}" style="display:none;">
                            <h4 class="add-option-subform__title">@lang('profile.add-options.option.'.$option)</h4>
                            @switch($option)
                                @case('license')
                                    @include('pages.profile-options.licenses', ['data' => $subscriber->licenses ?? []])
                                    @break
                                @case('diploma')
                                    @include('pages.profile-options.diplomas', ['data' => $subscriber->diplomas ?? []])
                                    @break
                                @case('promotion')
                                    @include('pages.profile-options.promotions', ['data' => $subscriber->promotions ?? []])
                                    @break
                                @case('image')
                                    @include('pages.profile-options.photos', ['data' => $subscriber->subscriberImages ?? []])
                                    @break
                                @case('job_offer')
                                    @include('pages.profile-options.joboffers', ['data' => $subscriber->jobOffers ?? []])
                                    @break
                                @case('estimation')
                                    @include('pages.profile-options.estimations', ['data' => $subscriber, 'registrationForm' => true])
                                    @break
                                @case('url')
                                    @include('pages.profile-options.url', ['data' => $subscriber, 'registrationForm' => true])
                                    @break
                            @endswitch
                        </div>
                    @endforeach

                    <div class="add-options-summary">
                        <h4>@lang('profile.add-options.summary')</h4>
                        <div id="fees-lines"></div>
                        <div class="add-options-summary__total">
                            <strong>Total</strong><strong id="fees-total">—</strong>
                        </div>
                        <p class="add-options-summary__note">@lang('profile.add-options.payment-note')</p>
                    </div>

                    <div class="content-card__footer">
                        <a href="{{ urlRouteName('profile') }}" class="call-to-action cta-alt">@lang('profile.add-options.back')</a>
                        <button type="submit" class="call-to-action">@lang('profile.add-options.submit')</button>
                    </div>

                {!! Form::close() !!}

                <script>
                (function () {
                    var optionPrices = @json($optionPrices, JSON_UNESCAPED_UNICODE);
                    var optionLabels = @json($optionLabels, JSON_UNESCAPED_UNICODE);
                    var websiteTiers = @json($tiers);
                    var urlLabel     = @json(__('profile.add-options.option.url'));
                    var emptyLabel   = @json(__('profile.add-options.empty'));
                    var fmt = new Intl.NumberFormat('fr-CA', { style: 'currency', currency: 'CAD' });
                    var linesEl = document.getElementById('fees-lines');
                    var totalEl = document.getElementById('fees-total');

                    function checked(name) { var cb = document.getElementById('opt_' + name); return !!(cb && cb.checked); }
                    function urlPrice() {
                        var sel = document.getElementById('url_forfait');
                        if (!sel || !sel.value) return 0;
                        var p = sel.value.split('-');
                        return (websiteTiers[p[0]] && websiteTiers[p[0]][p[1]]) ? Number(websiteTiers[p[0]][p[1]]) : 0;
                    }
                    function recompute() {
                        var lines = [], total = 0;
                        Object.keys(optionPrices).forEach(function (o) {
                            if (checked(o)) { var pr = Number(optionPrices[o]) || 0; lines.push([optionLabels[o] || o, pr]); total += pr; }
                        });
                        if (checked('url')) { var wp = urlPrice(); lines.push([urlLabel, wp]); total += wp; }
                        linesEl.innerHTML = lines.length
                            ? lines.map(function (l) {
                                return '<div class="add-options-summary__line"><span>' + l[0] + '</span><span>' + fmt.format(l[1]) + '</span></div>';
                            }).join('')
                            : '<div class="add-options-summary__empty">' + emptyLabel + '</div>';
                        totalEl.textContent = fmt.format(total);
                    }

                    // Révéler le sous-formulaire de l'option cochée + recalculer les frais.
                    document.querySelectorAll('.add-option-card__cb').forEach(function (cb) {
                        cb.addEventListener('change', function () {
                            var sf = document.getElementById('subform_' + cb.value);
                            if (sf) sf.style.display = cb.checked ? 'block' : 'none';
                            recompute();
                        });
                    });
                    document.addEventListener('change', function (e) {
                        if (e.target && e.target.id === 'url_forfait') recompute();
                    });
                    recompute();
                })();
                </script>
            @endif
        </div>
    </div>
</section>

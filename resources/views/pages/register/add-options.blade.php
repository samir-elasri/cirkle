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
                            </label>
                        @endforeach
                    </div>

                    @if(in_array('url', $availableOptions, true))
                        <div class="add-option-forfait" id="url_forfait_wrap" style="display:none;">
                            <label for="url_forfait">@lang('profile.add-options.url-forfait')</label>
                            <select name="url_forfait" id="url_forfait">
                                <option value="">—</option>
                                @foreach($tiers as $tier => $durations)
                                    @foreach($durations as $months => $price)
                                        <option value="{{ $tier }}-{{ $months }}">
                                            {{ $tier }}$ · {{ $months }} @lang('profile.add-options.months') — {{ prettyPrice($price) }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    @endif

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
                    var forfaitWrap = document.getElementById('url_forfait_wrap');
                    var forfaitSel  = document.getElementById('url_forfait');

                    function checked(name) { var cb = document.getElementById('opt_' + name); return !!(cb && cb.checked); }
                    function urlPrice() {
                        if (!forfaitSel || !forfaitSel.value) return 0;
                        var p = forfaitSel.value.split('-');
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
                    document.addEventListener('change', function (e) {
                        if (e.target && e.target.id === 'opt_url' && forfaitWrap) {
                            forfaitWrap.style.display = e.target.checked ? 'block' : 'none';
                        }
                        recompute();
                    });
                    recompute();
                })();
                </script>
            @endif
        </div>
    </div>
</section>

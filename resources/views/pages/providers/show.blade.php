{!! $blocs !!}

<section>
    <div class="optimal-content-width">
        <p>
            <div data-component="backBtn" data-text="{{ __('main.back') }}"></div>
        </p>

        <div class="content-card">

            {{-- Onglets de la fiche (feature #8) + rendu littéral MASTER 2350.
                 Styles inline temporaires : la rebuild SCSS est gelée (voir docs/gap-map.md) --}}
            <style>
                .fiche-literal { line-height: 1.7; }
                .fiche-literal__row { white-space: pre-wrap; }
                .fiche-literal__row--gap { margin-top: 1.1em; }
                .fiche-literal__custom { font-style: italic; color: #1a7a3a; }

                .fiche-tabs__nav { display: flex; flex-wrap: wrap; gap: .4em; border-bottom: 2px solid #e2e6df; margin: 18px 0 22px; }
                .fiche-tabs__btn {
                    appearance: none; background: transparent; border: 0; cursor: pointer;
                    padding: .65em 1.1em; font-weight: 600; color: #69716b;
                    border-bottom: 3px solid transparent; margin-bottom: -2px;
                }
                .fiche-tabs__btn:hover { color: #157a47; }
                .fiche-tabs__btn.is-active { color: #157a47; border-bottom-color: #1b9c5a; }
                .fiche-tabs__panel { display: none; }
                .fiche-tabs__panel.is-active { display: block; }
            </style>

            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;align-items:flex-start;margin-bottom:8px">
                <div>{{ __('main.member') }} {{ $provider->formatted_member_number ?? $provider->id }}</div>
                {{-- Cœur « favori fournisseur » : clients connectés (pas sa propre fiche) --}}
                @php($me = auth('subscribers')->user())
                @if ($me && (int) $me->id !== (int) $provider->id)
                    <span style="font-size:1.6em;cursor:pointer" title="{{ __('providers.add-favourite') }}">
                        @include('partials.providers.like')
                    </span>
                @endif
            </div>

            <h2>{{ $provider->company_name }}@if ($provider->profile_promotion_active) <span class="ck-promo-badge" title="{{ setting('promotion_title') }}">PROMO</span>@endif@if ($provider->profile_job_offer_active) <img class="ck-e-badge" src="{{ asset_with_version('/dist/img/cirkle-e-badge.png') }}" alt="Cirkle" title="{{ setting('job_offer_title') }}">@endif</h2>

            {{-- ===================== Navigation des onglets ===================== --}}
            <div class="fiche-tabs__nav" data-fiche-tabs>
                <button type="button" class="fiche-tabs__btn" data-tab-target="profil">{{ __('fiche.tab.profile') }}</button>
                <button type="button" class="fiche-tabs__btn is-active" data-tab-target="competence">{{ __('fiche.tab.competence') }}</button>

                @if ($images->count())
                    <button type="button" class="fiche-tabs__btn" data-tab-target="photos">{{ setting('image_title') }}</button>
                @endif
                @if ($licenses->count())
                    <button type="button" class="fiche-tabs__btn" data-tab-target="permis">{{ setting('license_title') }}</button>
                @endif
                @if ($diplomas->count())
                    <button type="button" class="fiche-tabs__btn" data-tab-target="diplomes">{{ __('fiche.tab.diplomas') }}</button>
                @endif
                @if ($provider->profile_estimation_active)
                    <button type="button" class="fiche-tabs__btn" data-tab-target="estimation">{{ setting('estimation_title') }}</button>
                @endif
                @if ($jobOffers->count())
                    <button type="button" class="fiche-tabs__btn" data-tab-target="recrutement">{{ setting('job_offer_title') }}</button>
                @endif
                @if ($promotions->count())
                    <button type="button" class="fiche-tabs__btn" data-tab-target="promotions">{{ setting('promotion_title') }}</button>
                @endif
            </div>

            {{-- ===================== Onglet Profil ===================== --}}
            <div class="fiche-tabs__panel" data-tab-panel="profil">
                <p>{{ $provider->main_description }}</p>

                <p>
                    {{ $provider->number }} {{ $provider->street }}, @if($provider->app) {{ $provider->app }}, @endif
                    {{ $provider->city }}, @if($provider->state?->title){{ $provider->state->title }}, @endif{{ $provider->postal_code }}
                </p>

                @if (logged_in())
                    <p>
                        <a class="call-to-action" href="mailto:{{ $provider->email }}">{{ __('providers.contact-this-provider.cta') }}</a>
                    </p>
                @endif

                @if ($provider->provider_type)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.provider_type') }}</div>
                        <div class="fiche-section__content pre">{{ __('providers.provider-type.' . $provider->provider_type) }}</div>
                    </div>
                @endif
                @if ($provider->legalForm)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.legal_form_id') }}</div>
                        <div class="fiche-section__content pre">{{ $provider->legalForm->title }}</div>
                    </div>
                @endif
                @if ($provider->owner_names)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.owner_names') }}</div>
                        <div class="fiche-section__content pre">{{ $provider->owner_names }}</div>
                    </div>
                @endif
                @if ($provider->federal_tax_number)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.federal_tax_number') }}</div>
                        <div class="fiche-section__content pre">{{ $provider->federal_tax_number }}</div>
                    </div>
                @endif
                @if ($provider->phone)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.phone') }}</div>
                        <div class="fiche-section__content pre">{{ $provider->phone }}</div>
                    </div>
                @endif
                @if ($provider->toll_free_phone)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.toll_free_phone') }}</div>
                        <div class="fiche-section__content pre">{{ $provider->toll_free_phone }}</div>
                    </div>
                @endif
                @if ($provider->fax)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.fax') }}</div>
                        <div class="fiche-section__content pre">{{ $provider->fax }}</div>
                    </div>
                @endif
                @if ($provider->start_date)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.start_date') }}</div>
                        <div class="fiche-section__content pre">{{ prettyDate($provider->start_date) }}</div>
                    </div>
                @endif
                @if ($provider->insurance_coverage)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.insurance_coverage') }}</div>
                        <div class="fiche-section__content pre">{{ $provider->insurance_coverage }}</div>
                    </div>
                @endif
                @if ($provider->business_hours)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.business_hours') }}</div>
                        <div class="fiche-section__content pre">{{ $provider->business_hours }}</div>
                    </div>
                @endif

                <div class="fiche-section">
                    <div class="fiche-section__title">{{ __('auth.register.service_category_id') }}</div>
                    <div class="fiche-section__content pre">{{ $provider->serviceCategory?->serviceCategory?->title }} / {{ $provider->serviceCategory?->title }}</div>
                </div>

                @if (now()->isAfter($provider->profile_url_activation_datetime) && now()->isBefore($provider->end_date))
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ setting('url_title') }}</div>
                        <div class="fiche-section__content pre"><a class="see-more" href="{{ $provider->url }}" target="_blank">{{ __('providers.visit-website') }}</a></div>
                    </div>
                @endif
            </div>

            {{-- ===================== Onglet Compétence (par défaut) ===================== --}}
            <div class="fiche-tabs__panel is-active" data-tab-panel="competence">
                <div class="fiche-section">
                    <div class="fiche-section__title">{{ __('auth.register.services') }}</div>
                    <div class="fiche-section__content">
                        @include('partials.fiche-competence-literal', ['rows' => $services, 'gapRows' => $gapRows ?? []])
                    </div>
                </div>

                <div class="fiche-section">
                    <div class="fiche-section__title">{{ __('auth.register.capabilities') }}</div>
                    <div class="fiche-section__content">
                        @include('partials.fiche-competence-literal', ['rows' => $capabilities, 'gapRows' => $gapRows ?? []])
                    </div>
                </div>

                @if ($provider->other_service_descriptions)
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('auth.register.other_service_descriptions') }}</div>
                        <div class="fiche-section__content pre">{{ $provider->other_service_descriptions }}</div>
                    </div>
                @endif
            </div>

            {{-- ===================== Onglet Photos (option payante) ===================== --}}
            @if ($images->count())
                <div class="fiche-tabs__panel" data-tab-panel="photos">
                    <div data-component="carouselSwiper" data-slideshow-auto-play-speed="400" class="slideshow mini-slideshow">
                        <div class="mini-slideshow__header">
                            <h3>{{ setting('image_title') }}</h3>
                            <div class="mini-slideshow__header__arrows">
                                <div class="slick-prev"><i class="fas fa-caret-left"></i></div>
                                <div class="slick-next"><i class="fas fa-caret-right"></i></div>
                            </div>
                        </div>
                        <div class="slides mini-slides">
                            @foreach($images as $image)
                                <div class="slide photos mini-slide">
                                    <div>{{ Html::image($image->image, '', ['width' => 500]) }}</div>
                                    <div class="label-style small-label">{{ $image->legend }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===================== Onglet Permis / licences ===================== --}}
            @if ($licenses->count())
                <div class="fiche-tabs__panel" data-tab-panel="permis">
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ setting('license_title') }}</div>
                        <div class="fiche-section__content">
                            <div class="service-list">
                                @foreach($licenses as $license)
                                    <div class="service-single">
                                        <div class="service-single__content">
                                            <div>{{ $license->title }}</div>
                                            <div class="label-style small-label">{{ $license->description }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===================== Onglet Diplômes (option PDIPOMECK) ===================== --}}
            @if ($diplomas->count())
                <div class="fiche-tabs__panel" data-tab-panel="diplomes">
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ __('fiche.tab.diplomas') }}</div>
                        <div class="fiche-section__content">
                            <table class="diploma-table" style="width:100%;border-collapse:collapse">
                                <thead>
                                    <tr style="text-align:left;border-bottom:2px solid #e2e6df">
                                        <th style="padding:.5em .75em">{{ __('fiche.diploma.course') }}</th>
                                        <th style="padding:.5em .75em">{{ __('fiche.diploma.school') }}</th>
                                        <th style="padding:.5em .75em">{{ __('fiche.diploma.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($diplomas as $diploma)
                                        <tr style="border-bottom:1px solid #eef1ec">
                                            <td style="padding:.5em .75em">{{ $diploma->title }}</td>
                                            <td style="padding:.5em .75em">{{ $diploma->school }}</td>
                                            <td style="padding:.5em .75em">{{ $diploma->graduated_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===================== Onglet Estimation ===================== --}}
            @if ($provider->profile_estimation_active)
                <div class="fiche-tabs__panel" data-tab-panel="estimation">
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ setting('estimation_title') }}</div>
                        <div class="fiche-section__content">
                            <div style="display: flex; flex-direction: row; gap: 10px; margin-bottom: 24px;">
                                <span>{{ __('providers.estimation_cost') }}</span>
                                <span>{{ prettyPrice($provider->estimation_cost) }}</span>
                            </div>
                            <div class="estimations-container">
                                <div>{{ __('providers.accepts_cash') }}</div>
                                <div class="yes__no">
                                    <span class="{{$provider->accepts_cash ? 'active' : ''}}">{{ __('form.yes') }}</span><span class="{{!$provider->accepts_cash ? 'active' : ''}}">{{ __('form.no') }}</span>
                                </div>
                                <div>{{ __('providers.accepts_check') }}</div>
                                <div class="yes__no">
                                    <span class="{{$provider->accepts_check ? 'active' : ''}}">{{ __('form.yes') }}</span><span class="{{!$provider->accepts_check ? 'active' : ''}}">{{ __('form.no') }}</span>
                                </div>
                                <div>{{ __('providers.accepts_debit') }}</div>
                                <div class="yes__no">
                                    <span class="{{$provider->accepts_debit ? 'active' : ''}}">{{ __('form.yes') }}</span><span class="{{!$provider->accepts_debit ? 'active' : ''}}">{{ __('form.no') }}</span>
                                </div>
                                <div>{{ __('providers.accepts_credit') }}</div>
                                <div class="yes__no">
                                    <span class="{{$provider->accepts_credit ? 'active' : ''}}">{{ __('form.yes') }}</span><span class="{{!$provider->accepts_credit ? 'active' : ''}}">{{ __('form.no') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===================== Onglet Recrutement (offres d'emploi) ===================== --}}
            @if ($jobOffers->count())
                <div class="fiche-tabs__panel" data-tab-panel="recrutement">
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ setting('job_offer_title') }}</div>
                        <div class="fiche-section__content">
                            <div class="item-spacer__container">
                                @foreach($jobOffers as $jobOffer)
                                    <div>
                                        <div>{{ $jobOffer->title }}</div>
                                        <div class="label-style">{{ $jobOffer->description }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===================== Onglet Promotions (différé en v1, affiché si présent) ===================== --}}
            @if ($promotions->count())
                <div class="fiche-tabs__panel" data-tab-panel="promotions">
                    <div data-component="carouselSwiper" data-slideshow-auto-play-speed="400" class="slideshow mini-slideshow">
                        <div class="mini-slideshow__header">
                            <h3>{{ setting('promotion_title') }}</h3>
                            <div class="mini-slideshow__header__arrows">
                                <div class="slick-prev"><i class="fas fa-caret-left"></i></div>
                                <div class="slick-next"><i class="fas fa-caret-right"></i></div>
                            </div>
                        </div>
                        <div class="slides mini-slides">
                            @foreach($promotions as $promotion)
                                <div class="slide promotions mini-slide">
                                    <div>{{ Html::image($promotion->image, $promotion->legend, ['width' => 500]) }}</div>
                                    <div>
                                        <div style="color:black;" class="label-style small-label">{{ $promotion->title }}</div>
                                        <div class="label-style small-label">{{ $promotion->description }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===================== Évaluations façon Google (feature #10) ===================== --}}
            @php($me = auth('subscribers')->user())
            <div style="margin-top: 32px; border-top: 1px solid #e2e6df; padding-top: 24px;">
                <style>
                    .review { padding: 1em 0; border-bottom: 1px solid #eef1ec; }
                    .review__head { display: flex; justify-content: space-between; font-weight: 600; }
                    .review__comment { margin: .35em 0; }
                    .review__reply { margin: .6em 0 0 1.25em; padding: .6em .9em; background: #f3f6f2; border-left: 3px solid #1b9c5a; border-radius: 0 6px 6px 0; }
                    .review__reply-label { font-weight: 700; color: #157a47; font-size: .9em; }
                    .review__pending { color: #a85c14; font-style: italic; font-size: .9em; }
                    .review-form { margin: 1em 0 1.5em; padding: 1em 1.25em; background: #fafbf9; border: 1px solid #e2e6df; border-radius: 8px; }
                </style>

                <div class="evaluations-list__header">
                    <h3>{{ __('providers.evaluation') }}</h3>
                    <div>
                        <span class="label-style">{{ __('evaluation.average') }}</span>
                        @include('partials.starDisplayer', ['stars' => $provider->evaluations_average])
                        <span class="label-style">({{ $evaluations->count() }})</span>
                    </div>
                </div>

                {{-- Formulaire d'avis : clients connectés uniquement, jamais sur sa propre fiche --}}
                @if ($me && (int) $me->id !== (int) $provider->id)
                    <div class="review-form">
                        {{ Form::open(['url' => urlRouteName('evaluation.store')]) }}
                            {{ Form::hidden('provider_id', $provider->id) }}
                            <div class="form__column">
                                <label>{{ __('evaluation.rating') }}</label>
                                @include('partials.starSelector', ['name' => 'global_grade'])
                            </div>
                            <div class="form__column">
                                <label>{{ __('evaluation.comment') }}</label>
                                {{ Form::textarea('comment', null, ['rows' => 3]) }}
                            </div>
                            <button type="submit" class="call-to-action">{{ __('evaluation.leave-review') }}</button>
                        {{ Form::close() }}
                    </div>
                @elseif (!$me)
                    <p class="label-style">{{ __('evaluation.login-required') }}</p>
                @endif

                <div class="evaluations-list @if($evaluations->count() > 5) evaluations-list--scrollbar @endif">
                    @foreach ($evaluations as $evaluation)
                        <div class="review">
                            <div class="review__head">
                                <div>{{ $evaluation->client?->name }}</div>
                                <div>{{ prettyDate($evaluation->created_at) }}</div>
                            </div>
                            @include('partials.starDisplayer', ['stars' => $evaluation->global_grade])
                            <div class="review__comment label-style">{{ $evaluation->comment }}</div>

                            {{-- Réponse du fournisseur : visible publiquement seulement si approuvée --}}
                            @if ($evaluation->reply && $evaluation->reply_approved)
                                <div class="review__reply">
                                    <div class="review__reply-label">{{ __('evaluation.provider-reply') }}</div>
                                    <div class="label-style">{{ $evaluation->reply }}</div>
                                </div>
                            @endif

                            {{-- Le fournisseur évalué peut répondre (en attente d'approbation admin) --}}
                            @if ($me && (int) $me->id === (int) $provider->id)
                                @if ($evaluation->reply && !$evaluation->reply_approved)
                                    <div class="review__pending">{{ __('evaluation.reply-pending') }}</div>
                                @elseif (!$evaluation->reply)
                                    {{ Form::open(['url' => urlRouteName('evaluation.reply'), 'style' => 'margin-top:.5em']) }}
                                        {{ Form::hidden('evaluation_id', $evaluation->id) }}
                                        {{ Form::textarea('reply', null, ['rows' => 2, 'placeholder' => __('evaluation.reply-placeholder')]) }}
                                        <button type="submit" class="call-to-action">{{ __('evaluation.reply-submit') }}</button>
                                    {{ Form::close() }}
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Bascule des onglets : vanilla JS (build compilée gelée). Un nudge « resize » réveille
     les carrousels (slick/swiper) qui s'initialisent mal dans un onglet caché. --}}
<script>
    (function () {
        var nav = document.querySelector('[data-fiche-tabs]');
        if (!nav) { return; }
        var buttons = nav.querySelectorAll('[data-tab-target]');
        var panels = document.querySelectorAll('[data-tab-panel]');

        nav.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-tab-target]');
            if (!btn) { return; }
            var target = btn.getAttribute('data-tab-target');

            buttons.forEach(function (b) { b.classList.toggle('is-active', b === btn); });
            panels.forEach(function (p) {
                p.classList.toggle('is-active', p.getAttribute('data-tab-panel') === target);
            });

            window.dispatchEvent(new Event('resize'));
        });
    })();
</script>

{!! $blocs !!}

<section>
    <div class="optimal-content-width">
        <p>
            <div data-component="backBtn" data-text="{{ __('main.back') }}"></div>
        </p>

        {{-- « Réviser ma fiche » : bandeau montré au fournisseur qui prévisualise sa
             propre fiche avant qu'elle soit active/publiée. --}}
        @if (!empty($isOwnerPreview) && !$provider->active)
            <div style="background:#fff8e1;border:2px solid #ffd200;border-radius:10px;padding:12px 16px;margin-bottom:14px;font-weight:600">
                {{ app()->getLocale() === 'en'
                    ? '👁 Preview — this is how your sheet will appear to clients once it is published. Only you can see it right now.'
                    : '👁 Révision — voici comment votre fiche apparaîtra aux clients une fois publiée. Vous seul pouvez la voir pour le moment.' }}
            </div>
        @endif

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

            {{-- « @endif@if » collés ne compilent pas (le \B@ de Blade rate le 2e @if) :
                 la page fiche entière tombait en erreur 500 — badges sur des lignes séparées. --}}
            <h2>{{ $provider->company_name }}
                @if ($provider->profile_promotion_active)
                    <span class="ck-promo-badge" title="{{ setting('promotion_title') }}">PROMO</span>
                @endif
                @if ($provider->profile_job_offer_active)
                    <img class="ck-e-badge" src="{{ asset_with_version('/dist/img/cirkle-e-badge.png') }}" alt="Cirkle" title="{{ setting('job_offer_title') }}">
                @endif
            </h2>

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

            {{-- Onglet Permis : tablo de Denis 07.07 (type, emetteur large, no, debut, fin — sans status/comments) --}}
            @if ($licenses->count())
                @php($enL = app()->getLocale() === 'en')
                <div class="fiche-tabs__panel" data-tab-panel="permis">
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ setting('license_title') }}</div>
                        <div class="fiche-section__content">
                            <table class="license-table" style="width:100%;border-collapse:collapse">
                                <thead>
                                    <tr style="text-align:left;border-bottom:2px solid #e2e6df">
                                        <th style="padding:.5em .75em;width:18%">TYPE</th>
                                        <th style="padding:.5em .75em;width:42%">{{ $enL ? 'OFFICIAL NAME OF ISSUING AUTHORITY / ORGANIZATION' : "NOM OFFICIEL DE L'ÉMETTEUR / ORGANISME" }}</th>
                                        <th style="padding:.5em .75em;width:20%">{{ $enL ? 'PERMIT / LICENCE / MEMBERSHIP / REGISTRATION NO.' : 'NO DE PERMIS / LICENCE / MEMBRE / INSCRIPTION' }}</th>
                                        <th style="padding:.5em .75em;width:10%">{{ $enL ? 'START (YYYY/MM)' : 'DÉBUT (AAAA/MM)' }}</th>
                                        <th style="padding:.5em .75em;width:10%">{{ $enL ? 'EXPIRY (YYYY/MM)' : 'FIN (AAAA/MM)' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licenses as $license)
                                        <tr style="border-bottom:1px solid #eef1ec">
                                            <td style="padding:.5em .75em">{{ $license->title }}</td>
                                            {{-- anciens permis (avant le tablo) : le détail vivait dans description --}}
                                            <td style="padding:.5em .75em">{{ $license->issuer ?: $license->description }}</td>
                                            <td style="padding:.5em .75em">{{ $license->registration_number }}</td>
                                            <td style="padding:.5em .75em">{{ $license->start_date }}</td>
                                            <td style="padding:.5em .75em">{{ $license->expiry_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                                    {{-- + d'espace pour le NOM (Denis 07.07) --}}
                                    <tr style="text-align:left;border-bottom:2px solid #e2e6df">
                                        <th style="padding:.5em .75em;width:45%">{{ __('fiche.diploma.course') }}</th>
                                        <th style="padding:.5em .75em;width:40%">{{ __('fiche.diploma.school') }}</th>
                                        <th style="padding:.5em .75em;width:15%">{{ __('fiche.diploma.date') }}</th>
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
                @php($estData = json_decode($provider->estimation_json ?: '', true) ?: [])
                @php($estEn = app()->getLocale() === 'en')
                <div class="fiche-tabs__panel" data-tab-panel="estimation">
                    <div class="fiche-section">
                        <div class="fiche-section__title">{{ setting('estimation_title') }}</div>
                        <div class="fiche-section__content">
                        @if ($estData)
                            {{-- Formulaire 10A de Denis : seules les réponses COCHÉES s'affichent --}}
                            @php($producedLabels = [
                                'client_location' => $estEn ? "At the client's location" : 'Chez le client',
                                'gps_map' => $estEn ? 'Via the "GPS map"' : 'Via la « carte GPS »',
                                'client_photos' => $estEn ? 'Based on client-provided photos' : 'Via photos du client',
                                'client_video' => $estEn ? 'Based on client-provided video' : 'Via vidéo du client',
                            ])
                            @php($produced = array_keys(array_filter((array) ($estData['produced'] ?? []))))
                            @if ($produced)
                                <p><strong>{{ $estEn ? 'The estimate will be produced:' : "L'estimation sera produite :" }}</strong>
                                    {{ implode(' · ', array_map(static fn ($k) => $producedLabels[$k] ?? $k, $produced)) }}</p>
                            @endif
                            @php($costType = $estData['cost']['type'] ?? null)
                            @if ($costType)
                                <p><strong>{{ $estEn ? 'Cost of the estimate:' : "Coût de l'estimation :" }}</strong>
                                    @if ($costType === 'free') {{ $estEn ? 'Free of charge' : 'Gratuit' }}
                                    @elseif ($costType === 'on_site') {{ $estEn ? 'Payable on-site' : 'Payable sur place' }} {{ $estData['cost']['on_site_note'] ?? '' }}
                                    @elseif ($costType === 'on_site_credited') {{ $estEn ? 'Payable on-site and credited toward the cost of the work' : 'Payable sur place et crédité sur la facture des travaux' }} {{ $estData['cost']['credited_note'] ?? '' }}
                                    @else {{ $estData['cost']['other_note'] ?? ($estEn ? 'Other' : 'Autre') }}
                                    @endif</p>
                            @endif
                            @php($payLabels = [
                                'cash' => $estEn ? 'Cash' : 'En argent',
                                'cheque' => $estEn ? 'Cheque' : 'Par chèque',
                                'interac' => 'Interac',
                                'debit' => $estEn ? 'Debit cards' : 'Cartes de débit',
                                'credit' => $estEn ? 'Credit cards' : 'Cartes de crédit',
                            ])
                            @php($pays = [])
                            @foreach ($payLabels as $pk => $pl)
                                @if (!empty($estData['pay'][$pk]))
                                    @php($note = trim((string) ($estData['pay'][$pk . '_note'] ?? '')))
                                    @php($pays[] = $pl . ($note !== '' ? ' (' . $note . ')' : ''))
                                @endif
                            @endforeach
                            @if ($pays)
                                <p><strong>{{ $estEn ? 'Accepted payments:' : 'Nous acceptons le paiement :' }}</strong> {{ implode(' · ', $pays) }}</p>
                            @endif
                            @if (!empty($estData['appt']['call_note']) || !empty($estData['appt']['email_note']))
                                <p><strong>{{ $estEn ? 'Appointments & discussions:' : 'Pour rendez-vous et discussions :' }}</strong>
                                    @if (!empty($estData['appt']['call_note'])) {{ $estEn ? 'Call' : 'Appelez' }} : {{ $estData['appt']['call_note'] }} @endif
                                    @if (!empty($estData['appt']['email_note'])) — {{ $estEn ? 'Email' : 'Courriellez' }} : {{ $estData['appt']['email_note'] }} @endif</p>
                            @endif
                            @if (!empty($estData['cancellation_note']))
                                <p><strong>{{ $estEn ? 'Contract cancellation fees:' : "Frais de cancellation d'un contrat :" }}</strong> {{ $estData['cancellation_note'] }}</p>
                            @endif
                            @if (!empty($estData['other_note']))
                                <p><strong>{{ $estEn ? 'Other terms:' : 'Autres :' }}</strong> {{ $estData['other_note'] }}</p>
                            @endif
                            @if ($provider->estimation_sheet_image)
                                <p><strong>{{ $estEn ? "Picture of the supplier's estimation sheet:" : "Feuille d'estimation du fournisseur :" }}</strong></p>
                                <div>{{ Html::image($provider->estimation_sheet_image, '', ['style' => 'max-width:420px;border-radius:8px']) }}</div>
                            @endif
                        @else
                            {{-- anciennes inscriptions : champs coût + modes de paiement --}}
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
                        @endif
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

            {{-- ===================== Onglet Promotions =====================
                 Bloc de Denis (07.07) : « CHÈRES CLIENTS VOICI NOTRE PROMOTION EN
                 DÉTAILS » — titre, description, durée (début/fin), photos (option A/B). --}}
            @if ($promotions->count())
                @php($promoEn = app()->getLocale() === 'en')
                <div class="fiche-tabs__panel" data-tab-panel="promotions">
                    <div class="fiche-section">
                        <div class="fiche-section__title">
                            {{ setting('promotion_title') }} <span class="ck-promo-badge">PROMO</span>
                        </div>
                        <div class="fiche-section__content">
                            <p style="font-weight:700">
                                {{ $promoEn ? 'DEAR CLIENTS, HERE IS OUR PROMOTION IN DETAIL:' : 'CHÈRES CLIENTS VOICI NOTRE PROMOTION EN DÉTAILS :' }}
                            </p>
                            @foreach($promotions as $promotion)
                                @php($promoPhotos = json_decode($promotion->photos_json ?: '[]', true) ?: [])
                                <div style="border:2px solid #ffd200;border-radius:10px;padding:14px 18px;margin-bottom:14px;background:#fffdf2">
                                    @if ($promotion->title)
                                        <div style="font-weight:700;font-size:1.1em;color:#157a47">{{ $promotion->title }}</div>
                                    @endif
                                    @if ($promotion->description)
                                        <div style="margin:6px 0">{{ $promotion->description }}</div>
                                    @endif
                                    @if ($promotion->start_date || $promotion->end_date)
                                        <div style="font-weight:600;color:#b25a00">
                                            {{ $promoEn ? 'DURATION OF THE PROMOTION:' : 'DURÉE DE LA PROMOTION :' }}
                                            @if ($promotion->start_date) {{ $promoEn ? 'from' : 'du' }} {{ $promotion->start_date }} @endif
                                            @if ($promotion->end_date) {{ $promoEn ? 'to' : 'au' }} {{ $promotion->end_date }} @endif
                                        </div>
                                    @endif
                                    @if ($promotion->image)
                                        <div style="margin-top:8px">{{ Html::image($promotion->image, $promotion->legend, ['style' => 'max-width:420px;border-radius:8px']) }}</div>
                                    @endif
                                    @if ($promoPhotos)
                                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px">
                                            @foreach ($promoPhotos as $pp)
                                                {{ Html::image($pp, '', ['style' => 'width:140px;height:105px;object-fit:cover;border-radius:8px']) }}
                                            @endforeach
                                        </div>
                                    @endif
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

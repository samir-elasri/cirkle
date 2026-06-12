{!! $blocs !!}

<section>
    <div class="optimal-content-width">
        <p>
            <div data-component="backBtn" data-text="{{ __('main.back') }}"></div>
        </p>

        <div class="content-card">

            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;align-items:flex-start;margin-bottom:24px">
                <div>{{ __('main.member') }} {{ $provider->id }}</div>
                <div style="display:flex; gap:10px; flex-wrap: wrap">
                    @if ($promotions->count())
                        <a class="call-to-action" href="#promotions">{{ setting("promotion_title") }}</a>
                    @endif

                    @if ($licenses->count())
                        <a class="call-to-action" href="#licenses">{{ setting('license_title') }}</a>
                    @endif

                    @if ($images->count())
                        <a class="call-to-action" href="#images">{{ setting('image_title') }}</a>
                    @endif

                    @if ($provider->profile_estimation_active)
                        <a class="call-to-action" href="#estimations">{{ setting('estimation_title') }}</a>
                    @endif

                    @if ($jobOffers->count())
                        <a class="call-to-action" href="#jobOffers">{{ setting('job_offer_title') }}</a>
                    @endif
                </div>
            </div>

            {{-- @if ($provider->profile_image)
                <div>{{ Html::image($provider->profile_image, 'profil', ['width' => 400]) }}</div>
            @endif --}}
        

            <h2>{{ $provider->company_name }}</h2>
            <p>{{ $provider->main_description }}</p>

            <p>
                {{ $provider->number }} {{ $provider->street }}, @if($provider->app) {{ $provider->app }}, @endif
                {{ $provider->city }}, {{ $provider->state?->title }}, {{ $provider->postal_code }}
            </p>


            @if (logged_in())
                <p>
                    <a class="call-to-action" href="mailto:{{ $provider->email }}">{{ __('providers.contact-this-provider.cta') }}</a>
                    {{-- <button type="button" class="call-to-action" data-component="modal" data-modal-template="contact-provider-modal">
                        <script type="application/json">{!! json_encode([
                            'options' => [
                                'showCloseButton' => true,
                                'showConfirmButton' => false,
                                'title' => __('providers.contact-this-provider.title'),
                            ]], JSON_THROW_ON_ERROR) !!}
                        </script>
                        {{ __('providers.contact-this-provider.cta') }}
                    </button>
                    <template
                            id="contact-provider-modal">
                        {!! Form::open(['url' => urlRouteName('providers.contact'), 'class' => 'form']) !!}
                        <input type="hidden"
                            name="provider_id"
                            value="{{ $provider->id }}">
                        <p>{{ __('providers.contact-this-provider.description') }}</p>
                        <div class="form__column">
                            {!! Form::label('text', __('providers.contact-this-provider.text')) !!}
                            {!! Form::textarea('text', null, ['class' => 'form-control needed', 'id' => 'text', 'required' => true]) !!}
                        </div>
                        <button type="submit"
                                class="call-to-action">
                            {{ __('providers.contact-this-provider.submit') }}
                        </button>
                        {!! Form::close() !!}
                    </template> --}}
                </p>
            @endif
            
            @if ($provider->provider_type)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.provider_type') }}
                    </div>
                    <div class="fiche-section__content pre">{{ $provider->provider_type }}</div>
                </div>
            @endif

            @if ($provider->legalForm)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.legal_form_id') }}
                    </div>
                    <div class="fiche-section__content pre">{{ $provider->legalForm->title }}</div>
                </div>
            @endif
            @if ($provider->federal_tax_number)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.federal_tax_number') }}
                    </div>
                    <div class="fiche-section__content pre">{{ $provider->federal_tax_number }}</div>
                </div>
            @endif
            @if ($provider->phone)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.phone') }}
                    </div>
                    <div class="fiche-section__content pre">{{ $provider->phone }}</div>
                </div>
            @endif
            @if ($provider->toll_free_phone)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.toll_free_phone') }}
                    </div>
                    <div class="fiche-section__content pre">{{ $provider->toll_free_phone }}</div>
                </div>
            @endif
            @if ($provider->fax)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.fax') }}
                    </div>
                    <div class="fiche-section__content pre">{{ $provider->fax }}</div>
                </div>
            @endif
            @if ($provider->start_date)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.start_date') }}
                    </div>
                    <div class="fiche-section__content pre">{{ prettyDate($provider->start_date) }}</div>
                </div>
            @endif
            @if ($provider->insurance_coverage)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.insurance_coverage') }}
                    </div>
                    <div class="fiche-section__content pre">{{ $provider->insurance_coverage }}</div>
                </div>
            @endif
            @if ($provider->business_hours)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.business_hours') }}
                    </div>
                    <div class="fiche-section__content pre">{{ $provider->business_hours }}</div>
                </div>
            @endif

            <div class="fiche-section">
                <div class="fiche-section__title">
                    {{ __('auth.register.service_category_id') }}
                </div>
                <div class="fiche-section__content pre">{{ $provider->serviceCategory?->serviceCategory?->title }} / {{ $provider->serviceCategory?->title }}</div>
            </div>


            <div class="fiche-section">
                <div class="fiche-section__title">
                    {{ __('auth.register.services') }}
                </div>
                <div class="fiche-section__content">
                    <div class="service-list">
                        @foreach ($services as $service)
                            <div class="service-single">
                                <div class="service-single__content">
                                    <div class="service-single__title">
                                        {{ $service->service?->title }}
                                    </div>
                                    <div class="label-style small-label service-single__description">
                                        {{ $service->service?->description }}
                                        {{ $service->custom_value }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="fiche-section">
                <div class="fiche-section__title">
                    {{ __('auth.register.capabilities') }}
                </div>
                <div class="fiche-section__content">
                    <div class="service-list">
                        @foreach ($capabilities as $service)
                            <div class="service-single">
                                <div class="service-single__content">
                                    <div class="service-single__title">
                                        {{ $service->service?->title }}
                                    </div>
                                    <div class="label-style small-label service-single__description">
                                        {{ $service->service?->description }}
                                        {{ $service->custom_value }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if ($provider->other_service_descriptions)
                <div class="fiche-section">
                    <div class="fiche-section__title">
                        {{ __('auth.register.other_service_descriptions') }}
                    </div>
                    <div class="fiche-section__content pre">{{ $provider->other_service_descriptions }}</div>
                </div>
            @endif



            @if (now()->isAfter($provider->profile_url_activation_datetime) && now()->isBefore($provider->end_date))
                <div class="fiche-section">
                    <div class="fiche-section__title">{{ setting('url_title') }}</div>
                    <div class="fiche-section__content pre"><a class="see-more" href="{{ $provider->url }}" target="_blank">{{ __('providers.visit-website') }}</a></div>
                </div>
            @endif

            @if ($promotions->count())
                <div id="promotions">
                    <div data-component="carouselSwiper" data-slideshow-auto-play-speed="400" class="slideshow mini-slideshow">
                        <div class="mini-slideshow__header">
                            <h3>{{ setting('promotion_title') }}</h3>
                            <div class="mini-slideshow__header__arrows">
                                <div class="slick-prev">
                                    <i class="fas fa-caret-left"></i>
                                </div>
                                <div class="slick-next">
                                    <i class="fas fa-caret-right"></i>
                                </div>
                            </div>
                        </div>
                        <div class="slides mini-slides">
                            @foreach($promotions as $promotion)
                                <div class="slide promotions mini-slide">

                                    <div>
                                        {{ Html::image($promotion->image, $promotion->legend, ['width' => 500]) }}
                                    </div>
                                    <div>
                                        <div style="color:black;"
                                             class="label-style small-label">
                                            {{ $promotion->title }}
                                        </div>
                                        <div class="label-style small-label">
                                            {{ $promotion->description }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($licenses->count())
                <div id="licenses" class="fiche-section">
                    <div class="fiche-section__title">{{ setting('license_title') }}</div>
                    <div class="fiche-section__content">
                        <div class="service-list">
                            @foreach($licenses as $license)
                                <div class="service-single">
                                    <div class="service-single__content">
                                        <div>
                                            {{ $license->title }}
                                        </div>
                                        <div class="label-style small-label">
                                            {{ $license->description }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($images->count())
                <div id="images" data-component="carouselSwiper" data-slideshow-auto-play-speed="400" class="slideshow mini-slideshow">
                    <div class="mini-slideshow__header">
                        <h3>{{ setting('image_title') }}</h3>
                        <div class="mini-slideshow__header__arrows">
                            <div class="slick-prev">
                                <i class="fas fa-caret-left"></i>
                            </div>
                            <div class="slick-next">
                                <i class="fas fa-caret-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="slides mini-slides">
                        @foreach($images as $image)
                            <div class="slide photos mini-slide">
                                <div>
                                    {{ Html::image($image->image, '', ['width' => 500]) }}
                                </div>
                                <div class="label-style small-label">
                                    {{ $image->legend }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            @endif

            @if ($provider->profile_estimation_active)
                <div id="estimations" class="fiche-section">
                    <div class="fiche-section__title">
                        {{ setting('estimation_title') }}
                    </div>
                    <div class="fiche-section__content">
                        <div style="display: flex; flex-direction: row; gap: 10px; margin-bottom: 24px;">
                            <span>
                                {{ __('providers.estimation_cost') }}
                            </span>
                            <span>
                                {{ prettyPrice($provider->estimation_cost) }}
                            </span>
                        </div>
                        <div class="estimations-container">
                            <div>{{ __('providers.accepts_cash') }}</div>
                            <div class="yes__no">
                                <span class="{{$provider->accepts_cash ? 'active' : ''}}">{{ __('form.yes') }}</span><span
                                        class="{{!$provider->accepts_cash ? 'active' : ''}}">{{ __('form.no') }}</span>
                            </div>
                            <div>{{ __('providers.accepts_check') }}</div>
                            <div class="yes__no">
                                <span class="{{$provider->accepts_check ? 'active' : ''}}">{{ __('form.yes') }}</span><span
                                        class="{{!$provider->accepts_check ? 'active' : ''}}">{{ __('form.no') }}</span>
                            </div>
                            <div>{{ __('providers.accepts_debit') }}</div>
                            <div class="yes__no">
                                <span class="{{$provider->accepts_debit ? 'active' : ''}}">{{ __('form.yes') }}</span><span
                                        class="{{!$provider->accepts_debit ? 'active' : ''}}">{{ __('form.no') }}</span>
                            </div>
                            <div>{{ __('providers.accepts_credit') }}</div>
                            <div class="yes__no">
                                <span class="{{$provider->accepts_credit ? 'active' : ''}}">{{ __('form.yes') }}</span><span
                                        class="{{!$provider->accepts_credit ? 'active' : ''}}">{{ __('form.no') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($jobOffers->count())
                <div id="jobOffers" class="fiche-section">
                    <div class="fiche-section__title">
                        {{ setting('job_offer_title') }}
                    </div>
                    <div class="fiche-section__content">
                        <div class="item-spacer__container">
                            @foreach($jobOffers as $jobOffer)
                                <div>
                                    <div>
                                        {{ $jobOffer->title }}
                                    </div>
                                    <div class="label-style">
                                        {{ $jobOffer->description }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if (logged_in())
                <div>
                    <div class="evaluations-list__header">
                        <h3>{{ __('providers.evaluation') }}</h3>
                        <div>
                            <span class="label-style">
                                {{ __('providers.evaluations_average') }}
                            </span>
                            @include('partials.starDisplayer', ['stars' => $provider->evaluations_average])
                        </div>
                    </div>
                    <div class="evaluations-list item-spacer__container @if($evaluations->count() > 5) evaluations-list--scrollbar @endif">
                        @foreach ($evaluations as $evaluation)
                            <div>
                                <div class="evaluations-list__item-title">
                                    <div>
                                        {{ $evaluation->client->name }}
                                    </div>
                                    <div>
                                        {{ prettyDate($evaluation->created_at) }}
                                    </div>
                                </div>
                                <div class="evaluations-list__comment label-style">
                                    {{ $evaluation->comment }}
                                </div>
                                <div class="evaluations-list__star-row service-list">
                                    <div>
                                        @include('partials.starDisplayer', ['stars' => $evaluation->global_grade])
                                        <label>{{ __('evaluation.global_grade') }}</label>
                                    </div>
                                    <div>
                                        &nbsp;
                                    </div>
                                    <div>
                                        @include('partials.starDisplayer', ['stars' => $evaluation->service_quality_grade])
                                        <label>{{ __('evaluation.service_quality_grade') }}</label>
                                    </div>
                                    <div>
                                        @include('partials.starDisplayer', ['stars' => $evaluation->communication_grade])
                                        <label>{{ __('evaluation.communication_grade') }}</label>
                                    </div>
                                    <div>
                                        @include('partials.starDisplayer', ['stars' => $evaluation->reliability_grade])
                                        <label>{{ __('evaluation.reliability_grade') }}</label>
                                    </div>
                                    <div>
                                        @include('partials.starDisplayer', ['stars' => $evaluation->hourly_rate_grade])
                                        <label>{{ __('evaluation.hourly_rate_grade') }}</label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

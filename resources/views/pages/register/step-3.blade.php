{!! $blocs !!}

<section>
    <div class="optimal-content-width">
  
        <div class="content-card">
        
            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">Enregistrement du fournisseur</h3>
                    <div class="content-card__label">Enregistrement du fournisseur</div>
                </div>
                @include('partials.progress', ['length' => 5, 'active' => 3])
            </div>

            @include('partials.help-note', ['text' => __('main.help.register-step3')])

            {!! Form::open(['url' => urlRouteName('subscriber.register.storeStep3')]) !!}

                <div class="form__column ">
                    <div class="color-primary">{{ __('auth.register.provider_type') }}</div>
                    {{ __('providers.provider-type.' . session('registerFormData.provider_type')) }}
                </div>

                <div class="form__column ">
                    <div class="color-primary">{{ __('auth.register.service_category_id') }}</div>
                    {{ \App\Models\ServiceCategory::find(session('registerFormData.service_category_id'))->title }}
                </div>

                <div class="form__column ">
                    <div class="color-primary">{{ __('auth.register.company_name') }}</div>
                    {{ session('registerFormData.company_name') }}
                </div>

                <div class="form__column ">
                    <div class="color-primary">{{ __('auth.register.full_address') }}</div>
                    {{ session('registerFormData.street') }},
                    {{ session('registerFormData.city') }},
                    {{ session('registerFormData.postal_code') }}
                </div>

                <div class="form__column ">
                    <div class="color-primary">{{ __('auth.register.phone') }}</div>
                    {{ session('registerFormData.phone') }}
                </div>

                <div class="form__column ">
                    <div class="color-primary">{{ __('auth.register.email') }}</div>
                    {{ session('registerFormData.email') }}
                </div>

                <div class="form__column ">
                    <div class="color-primary">{{ __('auth.register.services') }}</div>
                    @foreach(\App\Models\Service::whereIn('id', session('registerFormData.services'))->get() as $service)
                        <div>
                            {{ $service->title }}
                            {{ session('registerFormData.service_input.' . $service->id) }}
                        </div>
                    @endforeach
                    @foreach((session('registerFormData.custom_services') ?? []) as $service)
                        <div>{{ $service }}</div>
                    @endforeach
                </div>

                <div class="form__column ">
                    <div class="color-primary">{{ __('auth.register.capabilities') }}</div>
                    @foreach(\App\Models\Service::whereIn('id', session('registerFormData.capabilities'))->get() as $service)
                        <div>
                            {{ $service->title }}
                            {{ session('registerFormData.capability_input.' . $service->id) }}
                        </div>
                    @endforeach
                    @foreach((session('registerFormData.custom_capabilities') ?? []) as $service)
                        <div>{{ $service }}</div>
                    @endforeach
                </div>

                <div class="content-card__footer">
                    <a href="{{ urlRouteName('register-supplier-step-2') }}" class="call-to-action">{{ __('main.previous') }}</a>
                    <button type="submit" class="call-to-action">
                        {{ __('main.next') }}
                    </button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>

{!! $blocs !!}

<section>
    <div class="optimal-content-width">
  
        <div class="content-card">

            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">
                        {{ __('auth.register.title.step2') }}
                    </h3>
                    <div class="content-card__label">
                        {{ __('auth.register.title.step2-subtitle') }}
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

                <div id="service-container">
                    @if($serviceCategoryID = old('service_category_id') ?? (isset($subscriber) ? $subscriber->service_category_id : (session('registerFormData.service_category_id') ?? '')))
                        @include('partials.service-form', ['serviceCategory' => \App\Models\ServiceCategory::find($serviceCategoryID)])
                    @endif
                </div>

                <div class="content-card__footer">
                    @if(isset($isEdit) && $isEdit)
                        <button type="submit" class="call-to-action">
                            {{ __('form.save') }}
                        </button>
                    @else
                        <a href="{{ urlRouteName('register-supplier-step-1') }}" class="call-to-action">{{ __('main.previous') }}</a>
                        <button type="submit" class="call-to-action">
                            {{ __('main.next') }}
                        </button>
                    @endif
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
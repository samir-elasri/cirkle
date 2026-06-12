{!! $blocs !!}
<section>
    <div class="optimal-content-width">
        {{ Form::model($provider, ['url' => urlRouteName('providers.update'), 'class' => 'form full-screen-form', 'files' => true]) }}
        {{ Form::hidden('id') }}

        <div class="form__column ">
            {{ Form::label('provider_type', __('providers.form.provider_type') . ' *') }}
            @foreach($errors->get('provider_type', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div class="ui selection dropdown fluid"
                 data-component="dropdown">
                {{ Form::hidden('provider_type') }}
                <i class="dropdown icon"></i>
                <div class="default text"></div>
                <div class="menu">
                    <div class="item"
                         data-value="residential">{{ __('providers.provider-type.residential') }}</div>
                    <div class="item"
                         data-value="business">{{ __('providers.provider-type.business') }}</div>
                </div>
            </div>
        </div>

        <div class="form__column ">
            {{ Form::label('company_name', __('providers.form.company_name') . ' *') }}
            @foreach($errors->get('company_name', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            {{ Form::text('company_name') }}
        </div>

        <div class="form__column ">
            {{ Form::label('main_description', __('providers.form.main_description') . ' *') }}
            @foreach($errors->get('main_description', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            {{ Form::textarea('main_description') }}
        </div>

        <div class="form__column ">
            @foreach($errors->get('profile_image', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            @if ($provider->profile_image)
                <div>
                    {{ Html::image($provider->profile_image, '', ['width' => 400, 'class' => 'profile-img']) }}
                </div>
            @endif
            {{ Form::label('profile_image', __('providers.form.profile_image')) }}
            {{ Form::file('profile_image', ['accept' => 'image/jpeg,image/png,image/gif']) }}
        </div>

        <h3>{{ __('providers.form.address') }} *</h3>
        <div class="form__column ">
            {{ Form::label('number', __('providers.form.number')) }}
            @foreach($errors->get('number', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            {{ Form::text('number') }}
        </div>
        <div class="form__column ">
            {{ Form::label('street', __('providers.form.street')) }}
            @foreach($errors->get('street', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            {{ Form::text('street') }}
        </div>
        <div class="form__column ">
            {{ Form::label('app', __('providers.form.app')) }}
            @foreach($errors->get('app', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            {{ Form::text('app') }}
        </div>
        <div class="form__column ">
            {{ Form::label('city', __('providers.form.city')) }}
            @foreach($errors->get('city', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            {{ Form::text('city') }}
        </div>
        <div class="form__column ">
            {{ Form::label('country_id', __('providers.form.country_id')) }}
            @foreach($errors->get('country_id', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div class="ui selection dropdown fluid"
                 data-component="dropdown">
                {{ Form::hidden('country_id') }}
                <i class="dropdown icon"></i>
                <div class="default text"></div>
                <div class="menu">
                    @foreach($countries as $country)
                        <div class="item"
                             data-value="{{ $country->id }}">{{ $country->title }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form__column ">
            {{ Form::label('state_id', __('providers.form.state_id')) }}
            @foreach($errors->get('state_id', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div data-component="filteredDropdown"
                 data-parent-input-id="country_id"
                 data-hide-if-parent-empty="true">
                <div class="ui selection dropdown fluid"
                     data-component="dropdown">
                    {{ Form::hidden('state_id') }}
                    <i class="dropdown icon"></i>
                    <div class="default text"></div>
                    <div class="menu">
                        @foreach($states as $state)
                            <div class="item"
                                 data-value="{{ $state->id }}"
                                 data-parent="{{ $state->country_id }}">
                                {{ $state->title }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="form__column ">
            {{ Form::label('postal_code', __('providers.form.postal_code')) }}
            @foreach($errors->get('postal_code', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            {{ Form::text('postal_code') }}
        </div>

        <h3>{{ __('providers.form.category-description') }}</h3>
        <div data-component="filteredSection"
             data-parent-input-id="service_category_id">
            @foreach ($categories as $category)
                <div data-parent="{{ $category->id }}"
                     class="hide">
                    {!! $category->provider_description !!}
                </div>
            @endforeach
        </div>

        <h3>{{ __('providers.form.served-zone') }}</h3>
        @if ($subscription->type === 'cities')
            <div data-component="postalCodeSelector"
                 class="form__column ">
                <script type="application/json">
                    {!! json_encode([
                        'max' => $subscription->max_postal_codes,
                        'servedPostalCodes' => $servedPostalCodes,
                        'translations' => [
                            'noPostalCode' => __('providers.postal-code-selector.no-postal-code'),
                            'maxReached' => __('providers.postal-code-selector.max-reached'),
                            'remaining' => __('providers.postal-code-selector.remaining'),
                            'alreadyHas' => __('providers.postal-code-selector.already-has'),
                        ]
                    ], JSON_THROW_ON_ERROR) !!}
                </script>
                <small style="color: red"
                       data-ref="messageContainer"></small>
                <input data-ref="searchInput"
                       type="text">
                <div data-ref="countContainer"></div>
                <div data-ref="listContainer"></div>
            </div>
        @endif
        @if ($subscription->type === 'state')
            <div data-component="googlePlaceAutocomplete"
                 data-type="administrative_area_level_1">
                <input type="hidden"
                       name="served_state"
                       value="{{ $provider->served_state }}">
                <input type="text"
                       value="{{ $provider->served_state }}"
                       id="served_state">
            </div>
        @endif
        @if ($subscription->type === 'country')
            <div data-component="googlePlaceAutocomplete"
                 data-type="country">
                <input type="hidden"
                       name="served_country"
                       value="{{ $provider->served_country }}">
                <input type="text"
                       value="{{ $provider->served_country }}"
                       id="served_country">
            </div>
        @endif

        <div class="form__column ">
            {{ Form::label('service_category_id', __('providers.form.service_category_id') . ' *') }}
            @foreach($errors->get('service_category_id', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div class="ui selection dropdown fluid search"
                 data-component="dropdown">
                {{ Form::hidden('service_category_id') }}
                <i class="dropdown icon"></i>
                <div class="default text"></div>
                <div class="menu">
                    @foreach ($categories as $category)
                        <div class="item"
                             data-value="{{ $category->id }}">{{ $category->title }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form__column ">
            {{ Form::label('subcategories', __('providers.form.subcategories') . ' *') }}
            @foreach($errors->get('subcategories', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div data-component="filteredDropdown"
                 data-parent-input-id="service_category_id">
                <div class="ui selection dropdown fluid multiple search"
                     data-component="dropdown">
                    <input type="hidden"
                           value="{{ implode(',', $selectedSubcategories->toArray()) }}"
                           name="subcategories"
                           id="subcategories">
                    <i class="dropdown icon"></i>
                    <div class="default text"></div>
                    <div class="menu">
                        @foreach ($subcategories as $category)
                            <div class="item hide"
                                 data-value="{{ $category->id }}"
                                 data-parent="{{ $category->service_category_id }}">
                                {{ $category->title }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="form__column ">
            {{ Form::label('services', __('providers.form.services') . ' *') }}
            @foreach($errors->get('services', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div data-component="filteredCheckboxes"
                 data-parent-input-id="subcategories">
                @foreach ($services as $service)
                    <div class="item hide"
                         data-parent="{{ $service->service_category_id }}">
                        <input type="checkbox"
                               class="checkbox"
                               name="services[]"
                               value="{{$service->id}}"
                               id="services-{{$service->id}}"
                               @if($selectedServices->contains($service->id)) checked @endif
                        >
                        <label data-component="popup"
                               for="services-{{$service->id}}">
                            {{ $service->title }}
                        </label>
                        <div class="ui popup transition">
                            {!! $service->serviceCategory?->provider_description !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form__column ">
            <label>{{ __('providers.form.other_service_descriptions') }}</label>
            {{ Form::label('fr[other_service_descriptions]', __('providers.form.fr')) }}
            @foreach($errors->get('fr[other_service_descriptions]', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            {{ Form::textarea('fr[other_service_descriptions]') }}
        </div>

        <div class="form__column ">
            {{ Form::label('en[other_service_descriptions]', __('providers.form.en')) }}
            @foreach($errors->get('en[other_service_descriptions]', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            {{ Form::textarea('en[other_service_descriptions]') }}
        </div>

        <button type="submit"
                class="call-to-action">{{ __('form.submit') }}</button>
        {{ Form::close() }}
    </div>
</section>

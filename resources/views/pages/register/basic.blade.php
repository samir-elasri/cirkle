{!! $blocs !!}

<section class="ck-auth ck-auth--single">
    <div class="optimal-content-width">

        <div class="content-card">

            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">{{ __('auth.register.title.basic') }}</h3>
                </div>
            </div>

            {!! Form::open(['up-submit' => 'false', 'url' => urlRouteName('subscriber.register.storeBasic')]) !!}
                <input type="hidden" name="preference_language" value="{{ App::getLocale() }}">

                <div class="form__column ">
                    <div class="form__row--error">
                        {{-- <label for="first_name" class="required">{{ __('auth.register.first_name') }}</label> --}}
                        @foreach($errors->get('first_name', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" placeholder="{{ __('auth.register.first_name') }}">
                </div>
                
                <div class="form__column ">
                    <div class="form__row--error">
                        {{-- <label for="last_name" class="required">{{ __('auth.register.last_name') }}</label> --}}
                        @foreach($errors->get('last_name', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" placeholder="{{ __('auth.register.last_name') }}">
                </div>

                {{-- <div class="form__column ">
                    {{-- <label for="search_location" class="required">{{ __('auth.register.search_location') }}</label>
                    <div data-component="googlePlaceAutocomplete"
                        class="postal-code__icon"
                        data-type="postal_code_prefix">
                        <input type="hidden"
                            name="search_location"
                            value="{{ old('search_location') }}">
                        <input data-ref="postalCodeEl"
                            type="text"
                            value="{{ old('search_location') }}"
                            name="displayPostalCode"
                             placeholder="{{ __('auth.register.search_location') }}"
                            id="postal_code">
                    </div>
                </div> --}}

                <div class="form__column ">
                    {{-- <label for="street" class="required">{{ __('auth.register.street') }}</label> --}}
                    @foreach($errors->get('street', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <input type="text" name="street" id="street" value="{{ old('street') }}" placeholder="{{ __('auth.register.street') }}">
                </div>

                <div class="form__column ">
                    {{-- <label for="city" class="required">{{ __('auth.register.city') }}</label> --}}
                    @foreach($errors->get('city', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <input type="text" name="city" id="city" value="{{ old('city') }}" placeholder="{{ __('auth.register.city') }}">
                </div>

                <div class="form__column ">
                    {{-- <label for="postal_code" class="required">{{ __('auth.register.postal_code') }}</label> --}}
                    @foreach($errors->get('postal_code', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" placeholder="{{ __('auth.register.postal_code') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        {{-- <label for="email" class="required">{{ __('auth.register.email') }}</label> --}}
                        @foreach($errors->get('email', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="{{ __('auth.register.email') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        {{-- <label for="password" class="required">{{ __('auth.register.password') }}</label> --}}
                        @foreach($errors->get('password', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="password" name="password" id="password" placeholder="{{ __('auth.register.password') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        {{-- <label for="password_confirmation" class="required">{{ __('auth.register.password_confirmation') }}</label> --}}
                        @foreach($errors->get('password', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="{{ __('auth.register.password_confirmation') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        @foreach($errors->get('accept_condition', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <sl-checkbox name="accept_condition" value="1"><a href="{!! urlRouteName('term-of-use') !!}" target="blank">{{ __('auth.register.terms') }}</a></sl-checkbox>
                </div>
                
                <div class="content-card__footer">
                    <button type="submit" class="call-to-action">
                        {{ __('main.submit') }}
                    </button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>

@include('partials.password-eye')

{!! $blocs !!}

<section class="ck-auth">
    <div class="optimal-content-width">
  
        <div class="content-card">
            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">Enregistrement du fournisseur</h3>
                    <div class="content-card__label">Enregistrement du fournisseur</div>
                </div>
            </div>

            @include('partials.help-note', ['text' => __('main.help.register-step6')])

            {!! Form::open(['up-submit' => 'false', 'url' => urlRouteName('subscriber.register.storeStep6')]) !!}
                <div class="form__column ">
                    <div class="color-primary">{{__('auth.choose-password') }}</div>
                    <div class="form__row--error">
                        {{-- <label for="password" class="required">{{ __('auth.register.password') }}</label> --}}
                        @foreach($errors->get('password', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="password" name="password" id="password" value="" placeholder="{{ __('auth.register.password') }}">
                </div>
                <div class="form__column ">
                    <div class="form__row--error">
                        {{-- <label for="password_confirmation" class="required">{{ __('auth.register.password_confirmation') }}</label> --}}
                        @foreach($errors->get('password_confirmation', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" value="" placeholder="{{ __('auth.register.password') }}">
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
                    <a href="{{ urlRouteName('register-supplier-step-4') }}" class="call-to-action">{{ __('main.previous') }}</a>

                    <button type="submit" class="call-to-action">
                        {{ __('main.submit') }}
                    </button>
                </div>

            {!! Form::close() !!}
        </div>
    </div>
</section>

@include('partials.password-eye')

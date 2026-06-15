{!! $blocs !!}

<section>
    <div class="optimal-content-width">
  
        <div class="content-card" data-component="step4">
            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">Enregistrement du fournisseur</h3>
                    <div class="content-card__label">Enregistrement du fournisseur</div>
                </div>
                @include('partials.progress', ['length' => 5, 'active' => 4])
            </div>

            {!! Form::open(['url' => urlRouteName('subscriber.register.storeStep4')]) !!}
                {{-- <div>
                    <img src="/dist/img/info.svg" alt="">
                    <a href="{{ urlRouteName('public-subscriptions') }}" target="_blank">Description complete</a>
                </div> --}}

                <div class="form__column ">
                    <div class="form__row--error">
                        <label for="subscription_id">{{ __('auth.register.subscription_id') }}</label>
                        @foreach($errors->get('subscription_id', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <sl-select data-ref="subscriptionElement" name="subscription_id" id="subscription_id" value="{{ old('subscription_id') ?? session('registerFormData.subscription_id') ?? '' }}">
                        @foreach($subscriptions as $subscription)
                            <sl-option value="{{ $subscription->id }}" data-price="{{ $subscription->subscriptionPrices->first()?->cost }}" data-max-postal-codes="{{ $subscription->max_postal_codes }}">
                                {{ $subscription->title }}
                                -
                                {{ prettyPrice($subscription->subscriptionPrices->first()?->cost) }}
                            </sl-option>
                        @endforeach
                    </sl-select>
                </div>

                <div class="form__column ">
                    <label for="postal_codes">{{ __('auth.register.postal_codes') }}</label>
                    @foreach($errors->get('postal_codes', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <div>Le forfait inclut <span data-ref="maxPostalCodesElement"></span> codes postaux.</div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap">
                        @for ($i = 0; $i < $subscriptions->max('max_postal_codes'); $i++)
                            <input style="width:7ch" data-ref="postalCodeInputs" data-i="{{$i}}" type="text" name="postal_codes[{{ $i }}]" value="{{ old('postal_codes.'.$i) ?? session('registerFormData.postal_codes.'.$i) ?? '' }}">
                        @endfor
                    </div>
                </div>

                @if(setting('registration_fee') > 0)
                <div class="form__column">
                    <div class="ui info message">
                        <i class="info circle icon"></i>
                        Note: {{ __('auth.register.registration_fee_note', ['amount' => prettyPrice(setting('registration_fee'))]) }}
                    </div>
                </div>
                @endif

                {{-- <div class="form__column">
                    <label>Sommaire d'abonnement</label>

                    <div data-ref="totalElement"></div>
                </div> --}}

                <div class="content-card__footer">
                    <a href="{{ urlRouteName('register-supplier-step-3') }}" class="call-to-action">{{ __('main.previous') }}</a>

                    <button type="submit" class="call-to-action">
                        {{ __('main.next') }}
                    </button>
                </div>

            {!! Form::close() !!}
        </div>
    </div>
</section>

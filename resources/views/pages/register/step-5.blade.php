{!! $blocs !!}

<section>
    <div class="optimal-content-width">
        <div class="content-card">
            <div class="content-card__header">
                <div>
                    @if(isset($isEdit) && $isEdit)
                        <h3 class="content-card__header--title">Modifier les options de profil</h3>
                        <div class="content-card__label">Modifiez vos options de profil actives</div>
                    @else
                        <h3 class="content-card__header--title">Enregistrement du fournisseur</h3>
                        <div class="content-card__label">Enregistrement du fournisseur</div>
                    @endif
                </div>
                @if(!isset($isEdit) || !$isEdit)
                    @include('partials.progress', ['length' => 5, 'active' => 5])
                @endif
            </div>

            {!! Form::open(['url' => isset($isEdit) && $isEdit ? urlRouteName('subscriber.profile.updateStep5') : urlRouteName('subscriber.register.storeStep5')]) !!}

                {{-- <div>
                    <img src="/dist/img/info.svg" alt="">
                    <a href="{{ urlRouteName('options-list') }}" target="_blank">Options</a>
                </div> --}}

                @foreach($profileOptions as $option)
                    @if(isset($isEdit) && $isEdit)
                        {{-- In edit mode, show options directly without hide class --}}
                        <div id="{{$option}}_details">
                            <details class="details" open>
                                <summary>{{setting("{$option}_title", "{$option}_title")}}</summary>
                                <div>
                                    @if ($option === 'license')
                                        @include('pages.profile-options.licenses', ['data' => $subscriber->licenses ?? []])
                                    @elseif ($option === 'promotion')
                                        @include('pages.profile-options.promotions', ['data' => $subscriber->promotions ?? []])
                                    @elseif ($option === 'image')
                                        @include('pages.profile-options.photos', ['data' => $subscriber->subscriberImages ?? []])
                                    @elseif ($option === 'estimation')
                                        @include('pages.profile-options.estimations', [
                                            'data' => $subscriber,
                                            'registrationForm' => !$isEdit,
                                        ])
                                    @elseif ($option === 'job_offer')
                                        @include('pages.profile-options.joboffers', ['data' => $subscriber->jobOffers ?? []])
                                    @elseif ($option === 'url')
                                        @include('pages.profile-options.url', [
                                            'data' => $subscriber,
                                            'registrationForm' => !$isEdit,
                                        ])
                                    @endif
                                </div>
                            </details>
                        </div>
                    @else
                        {{-- Registration mode - original behavior --}}
                        <div id="{{$option}}_details" class="hide">
                            <details class="details">
                                <summary>{{setting("{$option}_title", "{$option}_title")}}</summary>
                                <div>
                                    @if ($option === 'license')
                                        @include('pages.profile-options.licenses', ['data' => []])
                                    @elseif ($option === 'promotion')
                                        @include('pages.profile-options.promotions', ['data' => []])
                                    @elseif ($option === 'image')
                                        @include('pages.profile-options.photos', ['data' => []])
                                    @elseif ($option === 'estimation')
                                        @include('pages.profile-options.estimations', [
                                            'data' => $subscriber,
                                            'registrationForm' => true,
                                        ])
                                    @elseif ($option === 'job_offer')
                                        @include('pages.profile-options.joboffers', ['data' => []])
                                    @elseif ($option === 'url')
                                        @include('pages.profile-options.url', [
                                            'data' => $subscriber,
                                            'registrationForm' => true,
                                        ])
                                    @endif
                                </div>
                            </details>
                        </div>
                    @endif
                @endforeach

                @if(!isset($isEdit) || !$isEdit)
                    {{-- Only show dropdown in registration mode --}}
                    <sl-dropdown>
                        <button class="call-to-action" type="button" slot="trigger">+ Ajouter une option</button>
                        <div class="sl-dropdown-content">
                            @foreach($profileOptions as $option)
                                <div>
                                    <sl-checkbox
                                        data-component="step5Toggler"
                                        data-target="#{{ $option }}_details"
                                        name="{{ $option }}"
                                        @if(!empty(old($option)) || !empty(session('registerFormData.'.$option))) checked @endif
                                    >
                                        <h3 style="margin:0">{{setting("{$option}_title", "{$option}_title")}}</h3>
                                    </sl-checkbox>
                                </div>
                                <p>
                                    {{setting("{$option}_description", "{$option}_description")}}
                                </p>
                                {{-- {{ prettyPrice(setting("{$option}_price")) }} --}}
                            @endforeach
                        </div>
                    </sl-dropdown>
                    
                    @if(setting('registration_fee') > 0)
                    <div class="form__column" style="margin-top: 20px;">
                        <div class="ui segment">
                            <h4>Sommaire des frais</h4>
                            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                                <span>{{ setting('registration_fee_title') ?? 'Frais d\'inscription' }}:</span>
                                <span><strong>{{ prettyPrice(setting('registration_fee')) }}</strong></span>
                            </div>
                            <div style="border-top: 1px solid #ddd; padding-top: 10px; font-size: 0.9em; color: #666;">
                                <i class="info circle icon"></i>
                                Ces frais seront ajoutés à votre panier avec votre abonnement et options sélectionnées.
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

                <div class="content-card__footer">
                    @if(!isset($isEdit) || !$isEdit)
                        <a href="{{ urlRouteName('register-supplier-step-4') }}" class="call-to-action">{{ __('main.previous') }}</a>
                    @endif

                    <button type="submit" class="call-to-action">
                        @if(isset($isEdit) && $isEdit)
                            {{ __('form.save') }}
                        @else
                            {{ __('main.submit') }}
                        @endif
                    </button>
                </div>

            {!! Form::close() !!}
        </div>
    </div>
</section>

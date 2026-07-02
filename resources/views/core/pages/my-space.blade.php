{!! $blocs !!}
@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
@php
    $supplierVisible = $subscriber->is_provider ? [
               'supplier-profile',
               'saved-searches',
               'profile-options',
           ] : [];
   $clientVisible = [
               'profile',
               'contacted-suppliers',
               'favorited-suppliers',
               'favorited-professions',
               'saved-searches',
               'profile-options',
               'invoices',
           ];
@endphp

<section
        data-component="mySpace"
        class="profile">
    <script type="application/json">
        {!! json_encode([
        'supplierVisible' => $supplierVisible,
        'availableTabs' => ['supplier' => $supplierVisible, 'client' => $clientVisible],
        'clientVisible' => $clientVisible,
]) !!}
    </script>

    <div class="optimal-content-width">
        <h1>@lang('auth.profile.greetings') {{ $subscriber->first_name }} {{ $subscriber->last_name }}</h1>
        @if ($subscriber->formatted_member_number)
            <p style="font-size:1.1em">@lang('main.memberNumber') : <strong style="display:inline-block;background:#ffd200;color:#161b26;padding:3px 14px;border-radius:999px;font-weight:800">{{ $subscriber->formatted_member_number }}</strong></p>
        @endif

        <div class="tabs tab-style tab-menu" style="display:none">
            <div class="tab tab-item active tab-style__single"
                 data-tab="client">@lang('profile.tabs.client')</div>
            @if($subscriber->is_provider)
                <div class="tab tab-item tab-style__single"
                     data-tab="supplier">@lang('profile.tabs.supplier')</div>
            @endif
        </div>

        @include('core.partials.spacing', ['spacing' => $default_bloc_spacing])

        <div class="tabcontent tab-window active content-card"
             data-tab="client">
            <div class="nested-tabs tab-menu">
                <div class="nested-tab tab-item active" data-tab="profile">@lang('profile.tabs.profile')</div>
                <div class="nested-tab tab-item" data-tab="contacted-suppliers">@lang('profile.tabs.contacted-suppliers')</div>
                <div class="nested-tab tab-item" data-tab="favorited-suppliers">@lang('profile.tabs.favorited-suppliers')</div>
                <div class="nested-tab tab-item" data-tab="favorited-professions">@lang('profile.tabs.favorited-professions')</div>
                <div class="nested-tab tab-item" data-tab="invoices">@lang('subscription.invoices')</div>
                <div class="nested-tab tab-item" data-tab="supplier-profile">@lang('profile.tabs.supplier-profile')</div>
                {{-- <div class="nested-tab tab-item" data-tab="saved-searches">@lang('profile.tabs.saved-searches')</div> --}}
            </div>
            <div class="nested-tabcontent tab-window active"
                 data-tab="profile">
                @include('core.pages.profile')
            </div>
            {{-- Historique des consultations (feature #11) : fiches récemment consultées --}}
            <div class="nested-tabcontent tab-window"
                 data-tab="contacted-suppliers">
                @forelse($subscriber->consultationHistory as $consultation)
                    @isset($consultation->viewedSubscriber)
                        @include('partials.providers.single', ['provider' => $consultation->viewedSubscriber])
                    @endisset
                @empty
                    {{ __('main.no-results') }}
                @endforelse
            </div>
            <div class="nested-tabcontent tab-window"
                 data-tab="favorited-suppliers">
                @forelse($subscriber->likedSubscribers as $liked)
                    @isset($liked->likedSubscriber)
                        @include('partials.providers.single', ['provider' => $liked->likedSubscriber])
                    @endisset
                @empty
                    {{ __('main.no-results') }}
                @endforelse
            </div>
            {{-- Professions favorites (feature #11) --}}
            <div class="nested-tabcontent tab-window"
                 data-tab="favorited-professions">
                <div class="single-list">
                    @forelse($subscriber->likedProfessions as $likedProfession)
                        @isset($likedProfession->serviceCategory)
                            <div class="single-list__item" style="padding:14px 20px">
                                <a href="{{ $likedProfession->serviceCategory->url }}">{{ $likedProfession->serviceCategory->title }}</a>
                            </div>
                        @endisset
                    @empty
                        {{ __('main.no-results') }}
                    @endforelse
                </div>
            </div>
            {{-- Factures PDF (feature #12) --}}
            <div class="nested-tabcontent tab-window"
                 data-tab="invoices">
                <div class="single-list">
                    @forelse($subscriber->orders()->where('is_cart', false)->latest()->get() as $order)
                        <div class="single-list__item" style="padding:14px 20px;display:flex;justify-content:space-between;gap:1em;align-items:center">
                            <div>
                                <strong>#{{ $order->id }}</strong>
                                <span class="muted">{{ prettyDate($order->created_at) }}</span>
                            </div>
                            <div>{{ prettyPrice($order->total_price) }}</div>
                            <a class="call-to-action" href="{{ urlRouteName('invoice', ['token' => $order->token]) }}" target="_blank">
                                {{ __('subscription.invoice-download') }}
                            </a>
                        </div>
                    @empty
                        {{ __('main.no-results') }}
                    @endforelse
                </div>
            </div>
            <div class="nested-tabcontent tab-window"
                 data-tab="saved-searches">
                <div class="single-list">
                    @forelse($searches as $search)
                        <div class="single-list__item">
                            <div>
                                {{prettyDateTime($search->created_at)}}
                            </div>
                            <div>
                                {{ $search->services?->implode('title', ', ') }}
                            </div>
                            <div>
                                {{ explode(', ', $search->postal_code ?? '')[0] }}
                            </div>
                            <button type="button"
                                    class="call-to-action"
                                    data-component="modal"
                                    data-modal-template="delete-search-modal">
                                <script type="application/json">{!! json_encode([
                            'options' => [
                                'showCloseButton' => true,
                                'showConfirmButton' => false,
                                'title' => __('profile.modal.delete-confirmation-title'),
                            ],
                            'values' => [
								'searchId' => $search->id
]
		                ], JSON_THROW_ON_ERROR) !!}</script>
                                @lang('profile.saved-search.delete')
                            </button>

                            <a class="call-to-action"
                               href="{{ $search->url }}">
                                @lang('profile.saved-search.view-results')
                            </a>
                        </div>
                    @empty
                        {{ __('main.no-results') }}
                    @endforelse
                </div>
            </div>

            @if($subscriber->is_provider)

                <div class="nested-tabcontent tab-window button-fullwidth"
                     data-tab="supplier-profile">
                    @if($subscriber->is_provider)
                        {{-- @if($subscriber->hasActiveSubscription())
                            <button class="call-to-action"
                                    data-component="modal"
                                    data-modal-template="pause-subscription-modal">
                                <script type="application/json">{!! json_encode([
                                'options' => [
                                    'showCloseButton' => true,
                                    'showConfirmButton' => false,
                                    'title' => __('profile.modal.pause-confirmation-title'),
                                ]
                            ], JSON_THROW_ON_ERROR) !!}</script>
                                @lang('subscription.cta.pause')
                            </button>

                            <button class="call-to-action"
                                    data-component="modal"
                                    data-modal-template="cancel-subscription-modal">
                                <script type="application/json">{!! json_encode([
                                'options' => [
                                    'showCloseButton' => true,
                                    'showConfirmButton' => false,
                                    'title' => __('profile.modal.pause-confirmation-title'),
                                ]
                            ], JSON_THROW_ON_ERROR) !!}</script>
                                @lang('subscription.cta.cancel')
                            </button>
                        @elseif($subscriber->hasPausedSubscription())
                            <button class="call-to-action"
                                    data-component="modal"
                                    data-modal-template="unpause-subscription-modal">
                                <script type="application/json">{!! json_encode([
                                'options' => [
                                    'showCloseButton' => true,
                                    'showConfirmButton' => false,
                                    'title' => __('profile.modal.pause-confirmation-title'),
                                ]
                            ], JSON_THROW_ON_ERROR) !!}</script>
                                @lang('subscription.cta.unpause')
                            </button>

                            <button class="call-to-action"
                                    data-component="modal"
                                    data-modal-template="cancel-subscription-modal">
                                <script type="application/json">{!! json_encode([
                                'options' => [
                                    'showCloseButton' => true,
                                    'showConfirmButton' => false,
                                    'title' => __('profile.modal.pause-confirmation-title'),
                                ]
                            ], JSON_THROW_ON_ERROR) !!}</script>
                                @lang('subscription.cta.cancel')
                            </button>

                        @endif --}}

                        {{-- Le formulaire 2350 est LE formulaire du fournisseur (Denis) :
                             accessible et modifiable EN TOUT TEMPS, qu'il soit publié ou non. --}}
                        @php
                            $hasActiveOptions = false;
                            $optionsToCheck = ['license', 'diploma', 'promotion', 'image', 'estimation', 'job_offer'];
                            foreach ($optionsToCheck as $option) {
                                if ($subscriber->{"profile_{$option}_active"}) {
                                    $hasActiveOptions = true;
                                    break;
                                }
                            }
                            if (!$hasActiveOptions && $subscriber->profile_url_activation_datetime) {
                                $hasActiveOptions = true;
                            }
                        @endphp

                        <div style="text-align:center;margin-bottom:1em">
                            <a class="call-to-action" style="font-size:1.1em;padding:.8em 1.6em" href="{{urlRouteName('edit-step-2')}}">
                                📋 @lang('profile.form2350.title')
                            </a>
                        </div>

                        <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center">
                            <div>
                                <a class="call-to-action cta-alt" href="{{urlRouteName('edit-step-1')}}">@lang('profile.step1.edit')</a>
                            </div>
                            {{-- Ajouter des options APRÈS l'inscription (achat) — demandé par Denis --}}
                            <div>
                                <a class="call-to-action" href="{{urlRouteName('add-options')}}">➕ @lang('profile.add-options.title')</a>
                            </div>
                            @if($hasActiveOptions)
                                <div>
                                    <a class="call-to-action cta-alt" href="{{urlRouteName('edit-step-5')}}">@lang('profile.options.edit')</a>
                                </div>
                            @endif
                            {{-- « Réviser ma fiche » (cahier de charges) : prévisualiser sa fiche
                                 telle que les clients la verront — accessible EN TOUT TEMPS,
                                 même avant publication (le contrôleur autorise le propriétaire). --}}
                            <div>
                                <a class="call-to-action cta-alt" href="{{urlRouteName('provider', ['id' => $subscriber->id])}}">
                                    👁 {{ app()->getLocale() === 'en' ? 'Review my sheet' : 'Réviser ma fiche' }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{--
            @if($subscriber->is_public)
                <div class="nested-tabcontent tab-window" data-tab="profile-options">
                    <div class="single-list">
                        @forelse($profileOptions as $option)
                            @php
                                $optionName = 'profile_' . $option . '_active';
                                $settingTitle = $option . '_title';
                            @endphp
							@if($subscriber->$optionName || ($option === 'url' && $subscriber->end_date && now()->isBefore($subscriber->end_date)))
                                <div class="single-list__item">
                                    <div>
                                        {{setting($settingTitle)}}
                                    </div>
                                    @php($dateTime = 'profile_' . $option . '_activation_datetime')
                                    @lang('profile.profile_options.date', ['date' => prettyDateTime($subscriber->$dateTime)])
                                    @if($option === 'url')
                                        @lang('profile.profile_options.end_date', ['date' => prettyDate($subscriber->end_date)])
                                    @endif
                                    <a href="{{urlRouteName("edit-profile-options-{$option}s")}}"
                                       class="call-to-action">
                                        @lang('main.modify')
                                    </a>
                                </div>
                            @endif
                        @empty
                            {{ __('main.no-results') }}
                        @endforelse

                        @if($subscriber->is_public)
                            <button class="call-to-action full-width-center"
                                    data-component="modal"
                                    data-modal-template="edit-profile-options-modal">
                                <script type="application/json">{!! json_encode([
                                'options' => [
                                    'showCloseButton' => true,
                                    'showConfirmButton' => false,
                                    'title' => __('profile.modal.edit-options'),
                                    'width' => 1000
                                ]
                            ], JSON_THROW_ON_ERROR) !!}</script>
                                @lang('profile.modal.edit-options')
                            </button>
                        @endif
                    </div>
                </div>
            @endif
            --}}

            {{-- <div class="nested-tabcontent tab-window"
                 data-tab="invoices">
                <div class="single-list">
                    @forelse($orders as $order)
                        <div class="single-list__item">
                            <div>
                                {{prettyDateTime($order->created_at)}}
                            </div>
                            <div>
                                {{prettyPrice($order->total_price)}}
                            </div>
                            <a class="call-to-action"
                               href="{{urlRouteName('order', ['token' => $order->token])}}"> @lang('profile.invoices.view') </a>
                        </div>
                    @empty
                        {{ __('main.no-results') }}
                    @endforelse
                </div>
            </div> --}}

        </div>
    </div>
</section>

<template id="delete-search-modal">
    <div>
        <a class="call-to-action" href="{{urlRouteName('delete-search')}}/_{searchId}">@lang('main.yes')</a>
    </div>
</template>

<template id="pause-subscription-modal">
    <div>
        <a class="call-to-action" href="{{urlRouteName('pause-subscription')}}">@lang('main.yes')</a>
    </div>
</template>

<template id="unpause-subscription-modal">
    <div>
        <a class="call-to-action" href="{{urlRouteName('unpause-subscription')}}">@lang('main.yes')</a>
    </div>
</template>

<template id="cancel-subscription-modal">
    <div>
        <a class="call-to-action" href="{{urlRouteName('cancel-subscription')}}">@lang('main.yes')</a>
    </div>
</template>

<template id="edit-profile-options-modal">
    <div>
        @include('partials.profileOptionsForm')
    </div>
</template>

@stack('profileOptionsScript')

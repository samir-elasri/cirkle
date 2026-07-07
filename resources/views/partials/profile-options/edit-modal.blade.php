{{-- Bouton « modifier » + modale d'édition d'un item d'option (permis, diplôme).
     Réutilise le composant modal générique (data-component="modal") qui clone le
     <template> par id — pas besoin du JS compilé (gelé). Form classique → recharge.
     IMPORTANT : uniquement pour un item DÉJÀ enregistré (id en base). Pendant
     l'inscription les items sont en session (sans id) — sinon urlRouteName(update)
     lève « Missing parameter: id » et casse TOUT le rendu de la liste. --}}
@if(!empty($item->id))
@php $editId = 'edit-' . $type . '-' . $item->id; @endphp

<button type="button" class="call-to-action edit-button"
        data-component="modal" data-modal-template="{{ $editId }}" title="{{ __('main.modify') }}">
    <script type="application/json">{!! json_encode([
        'options' => ['showCloseButton' => true, 'showConfirmButton' => false, 'title' => __('main.modify')],
    ], JSON_THROW_ON_ERROR) !!}</script>
    <i class="fas fa-pen"></i>
</button>

<template id="{{ $editId }}">
    <form class="form" method="POST" action="{{ urlRouteName('profile-option.update', ['type' => $type, 'id' => $item->id]) }}">
        @csrf
        @if($type === 'license')
            {{-- Tablo de Denis (07.07) : TYPE | ÉMETTEUR | NO | DÉBUT | FIN --}}
            <div class="form__column">
                <label>TYPE (FR)</label>
                <input name="fr[title]" value="{{ $item->translate('fr')?->title }}">
            </div>
            <div class="form__column">
                <label>TYPE (EN)</label>
                <input name="en[title]" value="{{ $item->translate('en')?->title }}">
            </div>
            <div class="form__column">
                <label>{{ app()->getLocale() === 'en' ? 'Official name of issuing authority / organization' : "Nom officiel de l'émetteur / organisme" }}</label>
                <input name="issuer" value="{{ $item->issuer }}">
            </div>
            <div class="form__column">
                <label>{{ app()->getLocale() === 'en' ? 'Permit / licence / membership / registration no.' : 'No de permis / licence / membre / inscription' }}</label>
                <input name="registration_number" value="{{ $item->registration_number }}">
            </div>
            <div class="form__column">
                <label>{{ app()->getLocale() === 'en' ? 'Start date (YYYY/MM)' : 'Date de début (AAAA/MM)' }}</label>
                <input name="start_date" value="{{ $item->start_date }}" maxlength="7">
            </div>
            <div class="form__column">
                <label>{{ app()->getLocale() === 'en' ? 'Expiry date (YYYY/MM)' : 'Date de fin (AAAA/MM)' }}</label>
                <input name="expiry_date" value="{{ $item->expiry_date }}" maxlength="7">
            </div>
        @elseif($type === 'diploma')
            <div class="form__column">
                <label>@lang('fiche.diploma.course') (FR)</label>
                <input name="fr[title]" value="{{ $item->translate('fr')?->title }}">
            </div>
            <div class="form__column">
                <label>@lang('fiche.diploma.course') (EN)</label>
                <input name="en[title]" value="{{ $item->translate('en')?->title }}">
            </div>
            <div class="form__column">
                <label>@lang('fiche.diploma.school')</label>
                <input name="school" value="{{ $item->school }}">
            </div>
            <div class="form__column">
                <label>@lang('fiche.diploma.date')</label>
                <input name="graduated_at" value="{{ $item->graduated_at }}" placeholder="2020/06">
            </div>
        @else
            <div class="form__column">
                <label>@lang('profile.options.fields.fr_title')</label>
                <input name="fr[title]" value="{{ $item->translate('fr')?->title }}">
            </div>
            <div class="form__column">
                <label>@lang('profile.options.fields.fr_description')</label>
                <textarea name="fr[description]">{{ $item->translate('fr')?->description }}</textarea>
            </div>
            <div class="form__column">
                <label>@lang('profile.options.fields.en_title')</label>
                <input name="en[title]" value="{{ $item->translate('en')?->title }}">
            </div>
            <div class="form__column">
                <label>@lang('profile.options.fields.en_description')</label>
                <textarea name="en[description]">{{ $item->translate('en')?->description }}</textarea>
            </div>
        @endif
        <button type="submit" class="call-to-action">@lang('form.save')</button>
    </form>
</template>
@endif

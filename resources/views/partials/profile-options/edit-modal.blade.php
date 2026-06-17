{{-- Bouton « modifier » + modale d'édition d'un item d'option (permis, diplôme).
     Réutilise le composant modal générique (data-component="modal") qui clone le
     <template> par id — pas besoin du JS compilé (gelé). Form classique → recharge. --}}
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
        @if($type === 'diploma')
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

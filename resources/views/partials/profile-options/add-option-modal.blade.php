<button type="button"
        class="call-to-action"
        data-component="modal"
        data-modal-template="add-new-modal{{$optionName}}">
    <script type="application/json">{!! json_encode([
        'options' => [
            'showCloseButton' => true,
            'showConfirmButton' => false,
            'title' => trans("profile.options.{$optionName}_add_modal_title"),
        ],
    ], JSON_THROW_ON_ERROR) !!}</script>
    <i class="fas fa-plus"></i> @lang('profile.options.add')
</button>

<template
        id="add-new-modal{{$optionName}}">

    {{-- Form NON-JS (POST direct + ?redirect=1) : fiable, ne dépend plus du composant
         JS compilé « addOption » (gelé/bugué). enctype pour les photos/promotion. --}}
    <form class="form" method="POST"
          action="{{ urlRouteName('profile-option.add', ['type' => $optionName, 'redirect' => 1]) }}"
          enctype="multipart/form-data">
            @csrf

            @if($optionName === 'subscriber_images')
                <input type="hidden" name="is_photos" value="1">
                <div class="form__column">
                    <label for="image[]">@lang('profile.options.fields.images')</label>
                    <input name="images[]" type="file" multiple>
                </div>
            @elseif($optionName === 'diploma')
                <div class="form__column">
                    <label for="fr[title]">@lang('fiche.diploma.course') (FR)</label>
                    <input name="fr[title]">
                </div>
                <div class="form__column">
                    <label for="en[title]">@lang('fiche.diploma.course') (EN)</label>
                    <input name="en[title]">
                </div>
                <div class="form__column">
                    <label for="school">@lang('fiche.diploma.school')</label>
                    <input name="school">
                </div>
                <div class="form__column">
                    <label for="graduated_at">@lang('fiche.diploma.date')</label>
                    <input name="graduated_at" placeholder="2020/06">
                </div>
            @else
            <div class="form__column">
                <label for="fr[title]">@lang('profile.options.fields.fr_title')</label>
                <input name="fr[title]">
            </div>
            <div class="form__column">
                <label for="fr[description]">@lang('profile.options.fields.fr_description')</label>
                <textarea name="fr[description]"></textarea>
            </div>
            <div class="form__column">
                <label for="en[title]">@lang('profile.options.fields.en_title')</label>
                <input name="en[title]">
            </div>
            <div class="form__column">
                <label for="en[description]">@lang('profile.options.fields.en_description')</label>
                <textarea name="en[description]"></textarea>
            </div>

            @if($optionName === 'promotions')
                <div class="form__column">
                    <label for="image">@lang('profile.options.fields.add-image')</label>
                    <input name="image" alt="" type="file">
                </div>
            @endif

            @endif
            <button type="submit" class="call-to-action">
                @lang('main.confirm')
            </button>
        </form>
</template>

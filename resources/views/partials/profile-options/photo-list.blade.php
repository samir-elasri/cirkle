@foreach($data as $photo)
    <div data-position="{{$photo->position}}"
         data-id="{{$photo->id}}"
         data-legend="{{$photo->legend}}"
         class="single-list__item list__item"
         style="padding: 20px;">

        <div>
            {{Html::image($photo->image, $photo->legend, ['width' => 200, 'class' => 'single-list__item__image'])}}
            <div class="label-style small-label">
                {{$photo->legend}}
            </div>
        </div>

        <div>
            <button type="button" class="call-to-action edit-legend">
                <i class="fas fa-edit"></i>
            </button>
        </div>

        <button type="button" class="call-to-action up-button">
            <i class="fas fa-arrow-up"></i>
        </button>

        <button type="button" class="call-to-action down-button">
            <i class="fas fa-arrow-down"></i>
        </button>

        {{-- Suppression : le bouton JS gelé ne marchait pas pour les photos. Pour un item
             déjà en base, lien direct (?redirect=1 → suppression + retour). --}}
        @if(!empty($photo->id))
            <a href="{{ urlRouteName('option-delete', ['type' => 'subscriber_images', 'id' => $photo->id, 'redirect' => 1]) }}"
               class="call-to-action delete-button-link" title="@lang('main.delete')"
               onclick="return confirm(@json(__('main.delete-modal.text')))">
                <i class="fas fa-times"></i>
            </a>
        @else
            <button type="button" class="call-to-action delete-button">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
@endforeach

<template
        id="edit-photo-modal">
    <form class="form">
        <div class="form__column">
            <label>
                @lang('profile.options.fields.legend')
            </label>
            <input type="text"
                   name="legend">
        </div>
    </form>
</template>
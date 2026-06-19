@foreach($data as $diploma)
    <div data-position="{{$diploma->position}}"
         data-id="{{$diploma->id}}"
         class="single-list__item list__item"
         style="padding: 20px;">
           <span>
                {{$diploma->title}}@if($diploma->school) — {{$diploma->school}}@endif @if($diploma->graduated_at)({{$diploma->graduated_at}})@endif
           </span>
        <button type="button" class="call-to-action up-button">
            <i class="fas fa-arrow-up"></i>
        </button>
        <button type="button" class="call-to-action down-button">
            <i class="fas fa-arrow-down"></i>
        </button>
        @include('partials.profile-options.edit-modal', ['item' => $diploma, 'type' => 'diploma'])
        @if(!empty($diploma->id))
            <a href="{{ urlRouteName('option-delete', ['type' => 'diplomas', 'id' => $diploma->id, 'redirect' => 1]) }}"
               class="call-to-action delete-button-link" title="@lang('main.delete')"
               onclick="return confirm(@json(__('main.delete-modal.text')))"><i class="fas fa-times"></i></a>
        @else
            <button type="button" class="call-to-action delete-button"><i class="fas fa-times"></i></button>
        @endif
    </div>
@endforeach

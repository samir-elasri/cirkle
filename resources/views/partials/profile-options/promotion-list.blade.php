@foreach($data as $promotion)
    <div data-position="{{$promotion->position}}"
         data-id="{{$promotion->id}}"
         class="list__item single-list__item"
         style="padding: 20px;">
           <span>
                {{$promotion->title}}
           </span>

        <div class="ui toggle checkbox">
            <input @if((bool)$promotion->in_progress) checked
                   @endif name="in_progress"
                   type="checkbox">
            <label>@lang('profile.options.fields.current')</label>
        </div>

        <button type="button" class="call-to-action up-button">
            <i class="fas fa-arrow-up"></i>
        </button>

        <button type="button" class="call-to-action down-button">
            <i class="fas fa-arrow-down"></i>
        </button>

        <button type="button" class="call-to-action delete-button">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endforeach

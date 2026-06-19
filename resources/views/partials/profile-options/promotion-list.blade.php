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

        @if(!empty($promotion->id))
            <a href="{{ urlRouteName('option-delete', ['type' => 'promotions', 'id' => $promotion->id, 'redirect' => 1]) }}"
               class="call-to-action delete-button-link" title="@lang('main.delete')"
               onclick="return confirm(@json(__('main.delete-modal.text')))"><i class="fas fa-times"></i></a>
        @else
            <button type="button" class="call-to-action delete-button"><i class="fas fa-times"></i></button>
        @endif
    </div>
@endforeach

@foreach($data as $promotion)
    <div data-position="{{$promotion->position}}"
         data-id="{{$promotion->id}}"
         class="list__item single-list__item"
         style="padding: 20px;">
           <span>
                {{$promotion->title}}
           </span>

        {{-- Toggle « en cours » en NON-JS : lien direct qui bascule + recharge --}}
        @if(!empty($promotion->id))
            <a href="{{ urlRouteName('promotion-toggle', ['id' => $promotion->id, 'redirect' => 1]) }}"
               class="call-to-action {{ $promotion->in_progress ? '' : 'cta-alt' }}">
                @lang('profile.options.fields.current') : {{ $promotion->in_progress ? '✓' : '—' }}
            </a>
        @else
            <div class="ui toggle checkbox">
                <input @if((bool)$promotion->in_progress) checked @endif name="in_progress" type="checkbox">
                <label>@lang('profile.options.fields.current')</label>
            </div>
        @endif

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

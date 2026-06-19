@foreach($data as $jobOffer)
    <div data-position="{{$jobOffer->position}}"
         data-id="{{$jobOffer->id}}"
         class="single-list__item list__item"
         style="padding: 20px;">
        <div class="job-offer-content">
            <h3>
                {{$jobOffer->title}}
            </h3>
            <div class="label-style" style="text-wrap:wrap;">
                {{$jobOffer->description}}
            </div>
        </div>

        {{-- Toggle « en recrutement » en NON-JS : lien direct qui bascule + recharge --}}
        @if(!empty($jobOffer->id))
            <a href="{{ urlRouteName('job-offer-toggle', ['id' => $jobOffer->id, 'redirect' => 1]) }}"
               class="call-to-action {{ $jobOffer->currently_recruiting ? '' : 'cta-alt' }}">
                @lang('profile.options.fields.currently-recruiting') : {{ $jobOffer->currently_recruiting ? '✓' : '—' }}
            </a>
        @else
            <div class="ui toggle checkbox">
                <input @if((bool)$jobOffer->currently_recruiting) checked @endif name="in_progress" type="checkbox">
                <label>@lang('profile.options.fields.currently-recruiting')</label>
            </div>
        @endif

        @if(!empty($jobOffer->id))
            <a href="{{ urlRouteName('option-delete', ['type' => 'job_offers', 'id' => $jobOffer->id, 'redirect' => 1]) }}"
               class="call-to-action delete-button-link" title="@lang('main.delete')"
               onclick="return confirm(@json(__('main.delete-modal.text')))"><i class="fas fa-times"></i></a>
        @else
            <button type="button" class="call-to-action delete-button"><i class="fas fa-times"></i></button>
        @endif
    </div>
@endforeach
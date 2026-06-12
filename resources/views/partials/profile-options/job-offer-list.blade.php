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

        <div class="ui toggle checkbox">
            <input @if((bool)$jobOffer->currently_recruiting) checked
                   @endif name="in_progress"
                   type="checkbox">
            <label>@lang('profile.options.fields.currently-recruiting')</label>
        </div>

        <button type="button" class="call-to-action delete-button">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endforeach
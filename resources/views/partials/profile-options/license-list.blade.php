@foreach($data as $license)
    <div data-position="{{$license->position}}"
         data-id="{{$license->id}}"
         class="single-list__item list__item"
         style="padding: 20px;">
           <span>
                {{ $license->title }}@if($license->issuer) — {{ $license->issuer }}@endif
                @if($license->registration_number) ({{ $license->registration_number }})@endif
                @if($license->start_date || $license->expiry_date) {{ trim($license->start_date . '–' . $license->expiry_date, '–') }}@endif
           </span>
        <button type="button" class="call-to-action up-button">
            <i class="fas fa-arrow-up"></i>
        </button>
        <button type="button" class="call-to-action down-button">
            <i class="fas fa-arrow-down"></i>
        </button>
        @include('partials.profile-options.edit-modal', ['item' => $license, 'type' => 'license'])
        @if(!empty($license->id))
            <a href="{{ urlRouteName('option-delete', ['type' => 'licenses', 'id' => $license->id, 'redirect' => 1]) }}"
               class="call-to-action delete-button-link" title="@lang('main.delete')"
               onclick="return confirm(@json(__('main.delete-modal.text')))"><i class="fas fa-times"></i></a>
        @else
            <button type="button" class="call-to-action delete-button"><i class="fas fa-times"></i></button>
        @endif
    </div>
@endforeach
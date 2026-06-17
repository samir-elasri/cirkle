@foreach($data as $license)
    <div data-position="{{$license->position}}"
         data-id="{{$license->id}}"
         class="single-list__item list__item"
         style="padding: 20px;">
           <span>
                {{$license->title}}
           </span>
        <button type="button" class="call-to-action up-button">
            <i class="fas fa-arrow-up"></i>
        </button>
        <button type="button" class="call-to-action down-button">
            <i class="fas fa-arrow-down"></i>
        </button>
        @include('partials.profile-options.edit-modal', ['item' => $license, 'type' => 'license'])
        <button type="button" class="call-to-action delete-button">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endforeach
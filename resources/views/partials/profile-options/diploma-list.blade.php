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
        <button type="button" class="call-to-action delete-button">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endforeach

{!! $blocs !!}
<section>
    <div class="optimal-content-width">
        <div class="search-result">
            <div class="search-result__section">
                @foreach($presentProfessions as $element)
                    <a href="{{ $element->url }}">{{ $element->title }}</a>
                @endforeach
            </div>
            {{-- <div class="search-result__section">
                @foreach($nonPresentProfessions as $element)
                    <span>{{ $element->title }}</span>
                @endforeach
            </div> --}}
        </div>
    </div>
</section>

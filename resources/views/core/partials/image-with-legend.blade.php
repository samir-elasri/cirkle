@if(!empty($image))

    <figure class="image-with-legend" style="width: {{ isset($width) ? $width . 'px' : 'inherit' }}; height: {{ isset($height) ? $height . 'px' : 'inherit' }}">
        <img class="image-with-legend__image" src="{{ $image }}" alt="{{ $alt ?? '' }}">
        
		@if(!empty($legend))
            <figcaption class="image-with-legend__legend">
				{{ $legend }}
			</figcaption>
        @endif

    </figure>

@endif

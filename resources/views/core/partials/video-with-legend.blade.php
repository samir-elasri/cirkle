@if(!empty($type) && !empty($video))

    <figure class="video-with-legend" style="width: {{ isset($width) ? $width . 'px' : '100%' }}">
		{{-- poster="{{ $image }}"  --}}
		@if ($type == 'video')
			<video
			src="{{ url($video) }}"
			data-video="{{ url($video) }}"
			controls id="local_{{ $id }}_{{ \Str::random(4) }}"
			data-component="video"
			data-poster="{{ $image }}"
			data-image="{{ $image }}"
			data-title="{{ $title }}"
			data-plyr-provider="{{ $type }}"
			></video>

		@else
			<div class="video-with-legend__container"
			id="extern_{{ $id }}_{{ \Str::random(4) }}"
			data-component="video"
			data-plyr-provider="{{ $type }}"
			data-plyr-embed-id="{{ $video }}"
			data-image="{{ $image }}"
			data-title="{{ $title }}"
			></div>
		@endif

		@if(isset($legend))

			<figcaption class="video-with-legend__legend">
				{{ $legend }}
			</figcaption>

		@endif

    </figure>

@endif
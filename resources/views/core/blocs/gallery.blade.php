@extends('core.layouts.bloc', ['wait_ready' => true])

@section('bloc-content-front')
	<div data-component="gallery" class="bloc-gallery__content">
		<div class="slidesList">
			@foreach($elements as $element)
				@if($element['type_element'] == 'local')
					<div class="slide" style="padding-bottom:0">
						<video
							src="{{  $element['filename'] }}"
							data-video="{{  $element['filename'] }}"
							controls id="video_{{ $element['id'] }}_{{ \Str::random(4) }}"
							data-component="video"
							data-image="{{ isset($element['image']) ? $element['image'] : '' }}"
							data-title="{{ $element['title'] }}"
							data-plyr-provider="{{ $element['type_element'] }}"
						></video>
					</div>
				@elseif($element['type_element'] == 'youtube' || $element['type_element'] == 'vimeo')
					<div style="padding-bottom:0" class="slide" data-id="{{ $element['id'] }}" data-type="{{ $element['type_element'] }}">
						<div
							id="video_{{ $element['id'] }}_{{ \Str::random(4) }}"
							data-component="video"
							data-plyr-provider="{{ $element['type_element'] }}"
							data-plyr-embed-id="{{ $element['filename'] }}"
							data-image="{{ isset($element['image']) ? $element['image'] : '' }}"
							data-title="{{ $element['title'] }}">
						</div>
					</div>
				@elseif($element['type_element'] == 'img')
					<div class="slide" data-type="{{ $element['type_element'] }}">
						<img data-lazy="{{ imageCache(Arr::get($element, 'image', ''), ['width' => setting()->optimal_content_width])  }}" alt="{{ $element['title'] }}" src="/dist/img/blank.gif">
					</div>
				@endif
				<div class="infosList">
					<div class="infos">{!! $element['description'] !!}</div>
				</div>
			@endforeach
		</div>
		<div class="thumbsListContainer">
			<div class="thumbsList">
				@foreach($elements as $element)
					@if($element['type_element'] == 'img')
						<div class="thumb {{ $element['type_element'] }}">
							{{Html::image('/dist/img/blank.gif', $element['title'], ['data-lazy' => $element['thumb'], 'width' => 290])}}
						</div>
					@elseif($element['type_element'] == 'local' || $element['type_element'] == 'youtube' || $element['type_element'] == 'vimeo')
						<div class="thumb {{ $element['type_element'] }}">
							{{Html::image($element['image'], $element['title'], ['width' => 290])}}
						</div>
					@endif
				@endforeach
			</div>
		</div>
	</div>
@stop

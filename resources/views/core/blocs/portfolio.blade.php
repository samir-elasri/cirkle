@extends('core.layouts.bloc')

@section('bloc-content-front')
	<div class="bloc-portfolio__content">
		<div class="bloc-portfolio__list" data-component="lightbox" data-id="lightbox">
			@foreach($elements as $index => $element)
				<div class="bloc-portfolio__single" data-index="{{ $index }}">
					<div class="bloc-portfolio__slide" data-type="{{ $element['type_element'] }}">
						{!! Html::image($element['image'], $element['title'], ['width' => 900]) !!}
					</div>
				</div>
			@endforeach
		</div>
	</div>
	<div id="lightbox" class="lightbox hide">
		<div class="lightbox-content">
			@if (!empty($elements))
				<div class="slideshow" id="lightbox-slick">
					@foreach($elements as $index => $slide)
						<div class="slide">
							@if($slide['type_element'] === 'local')
								<div class="bloc-portfolio__slide" style="padding-bottom:0">
									<video
										src="{{  $slide['filename'] }}"
										data-video="{{  $slide['filename'] }}"
										controls id="video_{{ $slide['id'] }}_{{ \Str::random(4) }}"
										data-component="video"
										data-image="{{ isset($slide['image']) ? $slide['image'] : '' }}"
										data-title="{{ $slide['title'] }}"
										data-plyr-provider="{{ $slide['type_element'] }}"
									></video>
								</div>
							@elseif($slide['type_element'] === 'youtube' || $slide['type_element'] === 'vimeo')
								<div style="padding-bottom:0" class="bloc-portfolio__slide" data-id="{{ $slide['id'] }}" data-type="{{ $slide['type_element'] }}">
									<div
										id="video_{{ $slide['id'] }}_{{ \Str::random(4) }}"
										data-component="video"
										data-plyr-provider="{{ $slide['type_element'] }}"
										data-plyr-embed-id="{{ $slide['filename'] }}"
										data-image="{{ isset($slide['image']) ? $slide['image'] : '' }}"
										data-title="{{ $slide['title'] }}">
									</div>
								</div>
							@elseif($slide['type_element'] === 'img')
								{!! Html::image($slide['image'], null, ['width' => 900]) !!}
								<div class="infosList">
									<div class="infos">{{ $slide['legend'] }}</div>
								</div>
							@endif
						</div>
					@endforeach
				</div>
			@endif
		</div>
	</div>
@stop

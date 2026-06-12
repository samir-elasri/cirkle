@extends('core.layouts.bloc', ['style' => 'min-height: 100%'])

@section('bloc-content-title')

	@if(!$accordion)
		@include('core.partials.bloc-title')
	@else
        <div class="bloc-text__accordion--no-margin-top"></div>
	@endif

@stop

@section('bloc-text-media')

	@if($media_type == 'image')

	@include('core.partials.image-with-legend', [
		'image' => $image,
		'legend' => $legend,
		'width' => $width
	])

	@elseif($media_type == 'map')

		@include('core.partials.google-maps', [
			'data' => $mapData,
			'width' => $width,
			'height' => $height
		])

	@else

		@include('core.partials.video-with-legend', [
			'id' => 'video_' . $id,
			'type' => $media_type,
			'image' => $image,
			'video' => $media_type == 'video' ? $video_filename : $video_url,
			'width' => $width,
			'legend' => $legend,
			'title' => $title
		])

	@endif

@stop

@section('bloc-content-front')

	@if(!$accordion && !empty($summary))
		<input class="bloc-text__summary-checkbox sr-only" aria-expanded="false" id="bloc-text-summary-{{ $id }}" type="checkbox">
		<div class="bloc-text__summary-content">
			<p>{{ $summary }}</p>
		</div>
	@endif

	@if($accordion)
		<input class="bloc-text__accordion-checkbox sr-only" aria-expanded="false" id="bloc-text-accordion-{{ $id }}" type="checkbox">
		<label for="bloc-text-accordion-{{ $id }}" class="bloc-text__accordion-label">
			@include('core.partials.bloc-title')
			<span class="bloc-text__accordion--plus fa fa-caret-right">{{-- Ouvrir --}}</span>
			<span class="bloc-text__accordion--minus fa fa-caret-down">{{-- Fermer --}}</span>
		</label>
	@endif

	<div class="bloc-text__content">

		@if(in_array($align, [null, 'left', 'right', 'top']))
			<div class="bloc-text__media
						bloc-text__media--{{ $media_type }}
						bloc-text__media--{{ empty($align) ? 'top' : $align }}
						bloc-text__media--over"
			>
				@yield('bloc-text-media')
			</div>
		@endif

		<div class="content-writable" style="{{ $relation == 'straight' ? 'overflow: hidden;' : '' }}">
			{!! $content ?? '' !!}

			@if($call_to_action_present)
				<a class="call-to-action" href="{{ isset($call_to_action_url) ? $call_to_action_url : '#' }}">
					{{ $call_to_action_label }}
				</a>
			@endif
		</div>

		@if(in_array($align, ['right', 'bottom']))
			<div class="bloc-text__media
						bloc-text__media--{{ $media_type }}
						bloc-text__media--{{ $align }}
						bloc-text__media--under"
			>
				@yield('bloc-text-media')
			</div>
		@endif


	</div>

	@if(!$accordion && !empty($summary))
		<label class="bloc-text__summary-label" for="bloc-text-summary-{{ $id }}">
			<span class="bloc-text__summary--plus fa fa-plus">{{-- Ouvrir --}}</span>
			<span class="bloc-text__summary--minus fa fa-minus">{{-- Fermer --}}</span>
		</label>
	@endif


@stop

@section('bloc-content-back')

	@if( ! empty($back_image))
		<div class="bloc-text__media bloc-text__media--back">
			<img src="{{ $back_image }}" alt="{{ $legend }}">
		</div>
	@endif

@stop

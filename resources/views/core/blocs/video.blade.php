@extends('core.layouts.bloc')

@section('bloc-content-front')

	<div class="content-writable">
		{!! $description ?? '' !!}
	</div>

	@include('core.partials.video-with-legend', [
		'id' => 'video_' . $id,
		'type' => $video_type,
		'image' => $image, 
		'video' => $video_type == 'video' ? $video_filename : $video_url,
		'legend' => $legend,
		'title' => $title
	])

@stop



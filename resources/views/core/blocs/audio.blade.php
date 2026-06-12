@extends('core.layouts.bloc', ['wait-ready' => true])

@section('bloc-content-front')
	@if ($image)
		<div class="bloc-audio__image">
			<img src="{{ $image }}" alt="{{ $title }}">
		</div>
	@endif
	
	<audio controls data-component="audio" data-id="audio_{{ $id }}">
		<source src="{{ $audio_filename }}">
		Your browser does not support the audio element.
	</audio>
  

@stop

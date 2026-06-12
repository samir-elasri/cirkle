@extends('core.layouts.bloc', ['wait_ready' => config('google.maps.active')])

@section('bloc-content-front')
	@if($bg_bleed)
		<div style="min-height: {{ $height ?? '400' }}px"></div>
	@else
		@include('core.partials.google-maps', ['data' => $mapData, 'bordered' => true])
	@endif
@stop

@section('bloc-content-back')
	@if($bg_bleed)
		@include('core.partials.google-maps', ['data' => $mapData])
	@endif
@stop

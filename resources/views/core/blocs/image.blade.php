@extends('core.layouts.bloc')

@section('bloc-content-front')

	@include('core.partials.image-with-legend', [
		'image' => $image, 
		'legend' => $legend
	])

@stop

@extends('core.layouts.bloc')

@section('bloc-content-front')

	{{-- Image cliquable si un lien est saisi dans l'admin (Steve 05.08 : « finaliser
	     la photo du bas de page avec un lien cliquable »). Lien vide = image simple. --}}
	@php($blocLink = trim((string) ($link_url ?? '')))
	@if($blocLink !== '')
		<a href="{{ $blocLink }}" style="display:block"
		   @if(!\Illuminate\Support\Str::startsWith($blocLink, ['/', '#'])) target="_blank" rel="noopener" @endif>
			@include('core.partials.image-with-legend', [
				'image' => $image,
				'legend' => $legend
			])
		</a>
	@else
		@include('core.partials.image-with-legend', [
			'image' => $image,
			'legend' => $legend
		])
	@endif

@stop

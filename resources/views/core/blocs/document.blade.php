@extends('core.layouts.bloc')

@section('bloc-content-front')
	@if (!empty($documents))
		@foreach($documents as $document)
			<div class='bloc-document__single'>
				<h4 class="bloc-document__single-label">{{ $document->title }}</h4>
				<p class="bloc-document__single-date">{{ explode(' ', $document->date)[0] }}</p>
				<p class="bloc-document__single-description">{!! $document->description !!}</p>
				<a href="/{{ $document->filename }}" download>
					@if ($document->vignette_image)
						<img src="{{ $document->vignette_image }}">
					@else
						Cliquez ici pour telecharger
					@endif
				</a>
			</div>
		@endforeach
	@endif
@stop

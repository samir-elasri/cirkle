@extends('core.layouts.bloc')

@section('bloc-content-front')

	@if(!empty($content) || $call_to_action_present)
	<div class="bloc-mini-cards__content content-writable">
		{!! $content !!}
		@if($call_to_action_present)
		<a class="call-to-action" href="{{ isset($call_to_action_url) ? $call_to_action_url : '#' }}">
			{{ $call_to_action_label }}
		</a>
		@endif
	</div>
	@endif

	@if($group)
	<div data-component="miniCards" data-tile-grid-width="{{ $group->width }}" data-tile-grid-spacing="5" class="bloc-mini-cards__grid">
		@foreach($group->cards as $card)
			@include('core.partials.mini-card-tile', array_merge($card->toArray(), ['bg_color' => $group->bg_color, 'width' => $group->width]))
		@endforeach
	</div>
	@endif

@stop

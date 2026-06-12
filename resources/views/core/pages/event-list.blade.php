
{!! $blocs !!}
@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section>
	<div class="optimal-content-width">
		<h2>@lang('events.next_events')</h2>
		<div class="event-list">
			<div data-component="tileGrid" data-tile-grid-width="380" data-tile-grid-spacing="40" class="event-list__body wait-ready">
				@foreach($events as $event) 
					@include('core.partials.event-tile', $event)
				@endforeach
			</div>
		</div>
		{!! $events->links() !!}
	</div>
</section>

@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section>
	<div class="optimal-content-width">
		<h2>@lang('events.passed_events')</h2>
		<div class="event-list">
			<div data-component="tileGrid" data-tile-grid-width="380" data-tile-grid-spacing="40" class="event-list__body wait-ready">
				@foreach($passedEvents as $passedEvent) 
					@include('core.partials.event-tile', $passedEvent)
				@endforeach
			</div>
		</div>
		{!! $passedEvents->links() !!}
	</div>
</section>
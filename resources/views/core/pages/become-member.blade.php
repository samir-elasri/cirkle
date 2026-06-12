{!! $blocs !!}
@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section>
	<div class="optimal-content-width">
		<div class="subscription-list">
			<div data-component="tileGrid" data-tile-grid-width="380" data-tile-grid-spacing="40" class="news-list__body subscription-list__body">
				@foreach($subscriptions as $subscription)
					@include('core.partials.subscription-tile', $subscription)
				@endforeach
			</div>
		</div>
		{{-- {!! $subscriptions->links() !!} --}}
	</div>
</section>
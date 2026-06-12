{!! $blocs !!}
@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section>
	<div class="optimal-content-width">
		<a class="call-to-action" href="{{$subscriber->orders->last()->url}}">@lang('cart.checkout.see-receipt')</a>
	</div>
</section>

@php use App\Models\Core\Purchase; @endphp

{!! $blocs !!}

@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section class="cart">
	<div class="optimal-content-width">
		@if(count($items))
			<div class="cart__empty">
				{{Form::open(['url' => urlRouteName('cart.empty'), 'class' => 'form'])}}
				@csrf
				{{Form::submit(__('cart.empty'), ['class'=> 'call-to-action'])}}
				{{Form::close()}}
			</div>
		@endif

		<div class="cart-list">

			@forelse($items as $item)
				<div class="cart-single">
					<div class="cart-single__head">
						<h2 class="cart-single__title">{{ $item->product_name }}</h2>
					</div>
					<div class="cart-single__body">
						<p>{{ $item->product_description }}</p>
						@foreach($item->getProductDetails() as $attribute)
							<div class="cart-single__{{$attribute}}">
								{!! $item->$attribute !!} {{ $item->getUnitType($attribute) }}
							</div>
						@endforeach
					</div>
					<div class="cart-single__footer">
						<div class="cart-single__price">
							<span class="cart__label">@lang('cart.totals.price') :</span>
							{{ prettyPrice($item->cost) }}
						</div>
					</div>
				</div>
			@empty
				@lang('cart.is_empty')
			@endforelse
		</div>

		@if(!empty($items))
			<div class="cart__summary">
				<span class="cart__label">@lang('cart.totals.sub_total') :</span>
				<div>{{prettyPrice($totals['sub_total'])}}</div>

				<span class="cart__label">@lang('cart.totals.tps') ({{setting('platform_tps')}}) :</span>
				<div>{{prettyPrice($totals['tps'])}}</div>

				<span class="cart__label">@lang('cart.totals.tvq') ({{setting('platform_tvq')}}) :</span>
				<div>{{prettyPrice($totals['tvq'])}}</div>

				<span class="cart__label">@lang('cart.totals.total') :</span>
				<div>{{prettyPrice($totals['total'])}}</div>

			</div>

			@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])

			<div class="cart__footer">

				@if($totals['total'] == 0)

					{{Form::open(['url' => urlRouteName('cart.buy_without_paying'), 'class' => 'form'])}}
					@csrf
					<div class="form__column">
						{{Form::submit(__('cart.confirm'), ['class'=> 'call-to-action'])}}
					</div>
					{{Form::close()}}

				@else
					<button data-component="stripe" class="call-to-action">
						<script type="application/json">{!! $stripeData !!}</script>
						@lang('cart.checkout.checkout')
					</button>
				@endif
			</div>
		@endif
	</div>
</section>

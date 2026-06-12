{!! $blocs !!}
@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section>
	<div class="optimal-content-width">

        <div style="max-width: 800px;">
            <div>{{ prettyDate($order->created_at) }}</div>
            <div>{{ setting('company_name') }}</div>
            <div>{!! setting('company_address') !!}</div>
            <div>#{{ $order->id }}</div>
            <div>{{ $order->subscriber->last_name }}</div>
            <div>{{ $order->subscriber->first_name }}</div>
            <div>
                {{ $order->subscriber->number }}
                {{ $order->subscriber->street }}
                {{ $order->subscriber->app }}
                <br>
                {{ $order->subscriber->city }}
                {{ $order->subscriber->state?->title }}
                {{ $order->subscriber->country?->title }}
                {{ $order->subscriber->postal_code }}
            </div>
        </div>

        @include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
		<div class="cart-list">
			<table width="100%" style="border-spacing: 20px 0; max-width: 800px;">
				<tbody>
					@foreach($items as $item)
						<tr>
							<td width="100%">{{$item->title}}</td>
                            <td style="width: 1%;white-space: nowrap;">{{$item->quantity}}</td>
							<td style="width: 1%;white-space: nowrap;">{{prettyPrice($item->cost)}}</td>
						</tr>
					@endforeach
					<tr>
						<td width="100%"></td>
						<td style="width: 1%;white-space: nowrap;">@lang('cart.totals.sub_total')</td>
						<td style="width: 1%;white-space: nowrap;">{{prettyPrice($order->sub_total_price)}}</td>
					</tr>

					@if($order->discount_amount)
						<tr>
							<td width="100%"></td>
							<td style="width: 1%;white-space: nowrap;">@lang('cart.coupon.coupon')</td>
							<td style="width: 1%;white-space: nowrap;">-{{ prettyPrice($order->discount_amount) }}</td>
						</tr>
					@endif
					<tr>
						<td width="100%"></td>
                        <td style="width: 1%;white-space: nowrap;">{{setting('tps_text')}}</td>
						<td style="width: 1%;white-space: nowrap;">{{prettyPrice($order->tps_price)}}</td>
					</tr>
					<tr>
						<td width="100%"></td>
						<td style="width: 1%;white-space: nowrap;">{{setting('tvq_text')}}</td>
						<td style="width: 1%;white-space: nowrap;">{{prettyPrice($order->tvq_price)}}</td>
					</tr>
					<tr>
						<td width="100%"></td>
						<td style="width: 1%;white-space: nowrap;">@lang('cart.totals.total')</td>
						<td style="width: 1%;white-space: nowrap;">{{prettyPrice($order->total_price)}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</section>

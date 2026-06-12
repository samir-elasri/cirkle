{{--{!! $blocs !!}--}}
{{--@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])--}}
{{--<section class="cart-list__section">--}}
{{--	<div class="optimal-content-width">--}}
{{--		<div class="cart-list ">--}}
{{--			<table>--}}
{{--				<tbody>--}}
{{--					@foreach($items as $item)--}}
{{--						<tr class="item-row">--}}
{{--							<td>@lang('cart.checkout.' . $item->product_type)</td>--}}
{{--							<td>{{$item->title}}</td>--}}
{{--							<td>{{prettyPrice($item->cost)}}</td>--}}
{{--						</tr>--}}
{{--					@endforeach--}}
{{--					<tr>--}}
{{--						<td></td>--}}
{{--						<td>@lang('cart.totals.sub_total')</td>--}}
{{--						<td>{{prettyPrice($totals['sub_total'])}}</td>--}}
{{--					</tr>--}}
{{--					@if($coupon)--}}
{{--						<tr>--}}
{{--							<td></td>--}}
{{--							<td>@lang('cart.totals.discounted')</td>--}}
{{--							<td>{{ prettyPrice($totals['discounted']) }}</td>--}}
{{--						</tr>--}}
{{--					@endif--}}
{{--					<tr>--}}
{{--						<td></td>--}}
{{--						<td>@lang('cart.totals.tps')</td>--}}
{{--						<td>{{prettyPrice($totals['tps'])}}</td>--}}
{{--					</tr>--}}
{{--					<tr>--}}
{{--						<td></td>--}}
{{--						<td>@lang('cart.totals.tvq')</td>--}}
{{--						<td>{{prettyPrice($totals['tvq'])}}</td>--}}
{{--					</tr>--}}
{{--					<tr>--}}
{{--						<td></td>--}}
{{--						<td>@lang('cart.totals.total')</td>--}}
{{--						<td>{{prettyPrice($totals['total'])}}</td>--}}
{{--					</tr>--}}
{{--				</tbody>--}}
{{--			</table>--}}
{{--		</div>--}}

{{--		@if($totals['total'] == 0)--}}
{{--			<div class="cart__checkout-link">--}}
{{--				{{Form::open(['url' => urlRouteName('cart.buy_without_paying'), 'class' => 'form'])}}@csrf--}}
{{--					<div class="form__column">--}}
{{--						{{Form::submit(__('cart.confirm'), ['class'=> 'call-to-action'])}}--}}
{{--					</div>--}}
{{--				{{Form::close()}}--}}
{{--			</div>--}}
{{--		@else--}}
{{--			<div class="cart__checkout-link">--}}
{{--				<script src="https://www.paypal.com/sdk/js?currency=CAD&client-id={{env('PAYPAL_CLIENT_ID','sb')}}"></script>--}}
{{--				<div id="paypal-button-container"></div>--}}
{{--				<script>--}}
{{--					paypal.Buttons({--}}
{{--						createOrder: (data, actions) => {--}}
{{--							// This function sets up the details of the transaction, including the amount and line item details.--}}
{{--							return actions.order.create({--}}
{{--								purchase_units: [{--}}
{{--									amount: {--}}
{{--										currency_code: 'CAD',--}}
{{--										value: "{{round($totals['total'], 2)}}",--}}
{{--										breakdown: {--}}
{{--											item_total: {--}}
{{--												currency_code:'CAD',--}}
{{--												value:"{{ $coupon ? round($totals['discounted'], 2) : round($totals['sub_total'], 2) }}",--}}
{{--											},--}}
{{--											tax_total:{--}}
{{--												currency_code:'CAD',--}}
{{--												value:"{{round($totals['tps'] + $totals['tvq'], 2) }}",--}}
{{--											}--}}
{{--										},--}}
{{--									},--}}
{{--									items:[--}}
{{--										@foreach($items as $item)--}}
{{--											{--}}
{{--												name: '{{$item->title}}',--}}
{{--												description: "@lang('cart.checkout.' . $item->product_type)",--}}
{{--												unit_amount: {--}}
{{--													currency_code: 'CAD',--}}
{{--													value: '{{round($item->cost, 2)}}',--}}
{{--												},--}}
{{--												quantity: '1',--}}
{{--											},--}}
{{--										@endforeach--}}
{{--									]--}}
{{--								}]--}}
{{--							});--}}
{{--						},--}}
{{--						onApprove: (data, actions) => {--}}
{{--							// This function captures the funds from the transaction.--}}
{{--							return actions.order.capture().then((details) => {--}}
{{--								// Call your server to save the transaction--}}
{{--								return fetch("{{urlRouteName('cart.buy')}}", {--}}
{{--									method: 'post',--}}
{{--									headers: {--}}
{{--										'content-type': 'application/json',--}}
{{--										'X-CSRF-TOKEN': "{{ csrf_token() }}"--}}
{{--									},--}}
{{--									body: JSON.stringify({--}}
{{--										orderID: data.orderID--}}
{{--									})--}}
{{--								}).then(() => {--}}
{{--									window.location.replace("{{urlRouteName('purchase-confirmation')}}")--}}
{{--								});--}}
{{--							});--}}
{{--						}--}}
{{--					}).render('#paypal-button-container');--}}
{{--					//This function displays Smart Payment Buttons on your web page.--}}
{{--				</script>--}}
{{--			</div>--}}
{{--		@endif--}}
{{--	</div>--}}
{{--</section>--}}

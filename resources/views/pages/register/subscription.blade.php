{!! $blocs !!}
<section class="page with-background register__section">
	<div class="page__content register optimal-content-width">
		<h1>
			@lang('auth.register.title')
		</h1>

		@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])

		{!! Form::open(['class' => 'form register-form']) !!}
			<div>
				<table width="100%" class="subscription-table" cellpadding="0">
					@foreach($rows as $key => $row)
						<tr>
							<td>
								@lang("subscription.$key")
							</td>
							@if ($key === 'subscriptionPrices')
								@foreach($row as $col)
									<td class="col-{{$ids[$loop->index]}} {{$recommended[$loop->index] ? 'recommended':''}}">
										<div> @if(!$col->first())
												@lang('subscription.price.free')
											@else
												<table width="100%">
													@foreach($col as $price)
														<tr>
															<td>
																{{prettyPrice($price?->cost)}} / @if($price->month_duration)
																	{{($price->month_duration > 1) ?($price->month_duration . ' ' . trans('subscription.price.month')) : trans('subscription.price.month')}}
																@elseif($price->year_duration)
																	{{($price->year_duration > 1) ?($price->year_duration. ' '. trans('subscription.price.years')) : trans('subscription.price.year')}}
																@endif
															</td>
														</tr>
														<tr>
															<td>
																{{$price?->text}}
															</td>
														</tr>
													@endforeach
												</table>
											@endif
										</div>
									</td>
								@endforeach
							@else
								@foreach($row as $col)
									<td class="col-{{$ids[$loop->index]}} {{$recommended[$loop->index] ? 'recommended':''}} {{($key === 'title')? 'top-row' : ''}}">
										<div>
											{{$col}}
										</div>
									</td>
								@endforeach
							@endif
						</tr>
					@endforeach
					<tr>
						<td></td>
						@foreach($ids as $id)
							<td class="col-{{$id}} {{$recommended[$loop->index] ? 'recommended':''}}">
								<div>
									<button
										type="submit"
										formaction="{{ urlRouteName('subscriber.register.storeSubscription')}}?product_id={{$id}}"
										class="call-to-action">
										@lang('subscription.buy')
									</button>
								</div>
							</td>
						@endforeach
					</tr>
					<tr>
						<td>@lang('subscription.zone_serviced')</td>
						@foreach($types as $type)
							<td class="bottom-row col-{{$ids[$loop->index]}} {{$recommended[$loop->index] ? 'recommended':''}}">
								<div>
									{{ setting("{$type}_serviced_label") }}
								</div>
							</td>
						@endforeach
					</tr>
				</table>
			</div>
		{!! Form::close() !!}
	</div>
</section>

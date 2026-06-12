@include('core.partials.spacing', ['spacing' => $page_top_spacing])

<section>
	<div class="optimal-content-width">
		<div class="event__header">
			<div>
				<div class="event__image"><img
						src="{{ empty($image) ? setting()->events_default_image : $image }}"
						alt="{{ $title }}"/></div>
				<div class="event__legend">{!! $legend !!}</div>
			</div>
			<div>
				<div class="event__date">{{ prettyDate($start_datetime) }}</div>
				<div class="event__address ">
					<h3 style="margin-top: 15px">@lang('main.events.address')</h3>
					<ul>
						<li>@lang('main.events.street_number'): {{ !empty($number) ? $number : '' }}</li>
						<li>@lang('main.events.street'): {{ !empty($street) ? $street : '' }}</li>
						<li>@lang('main.events.flat'): {{ !empty($app) ? $app : '' }}</li>
						<li>@lang('main.events.zip_code'): {{ !empty($zip_code) ? $zip_code : '' }}</li>
						<li>@lang('main.events.city'): {{ !empty($city) ? $city : '' }}</li>
						<li>@lang('main.events.state'): {{ !empty($state_id) ? $state : '' }}</li>
						<li>@lang('main.events.country'): {{ !empty($country_id) ? $country : '' }}</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="news__places">
			@if($placesLeft !== null)
				<h3 class="event__places-left" style="margin-top: 40px;">@lang('main.events.places_left')
					: {{$placesLeft}}</h3>
			@endif
			<h3 style="margin-top: 15px">@lang('main.events.price_categories')</h3>

			<?php

			if (logged_in()) {
				$subscriber = Auth::guard('subscribers')->user();
			} else {
				$subscriber = null;
			}
			if ($subscriber !== null && $subscriber->hasActiveSubscription()) {
				$yourPrice = $sub_price;
				$getMemberPriceNotice = false;
			} else {
				$yourPrice = $non_sub_price;
				$getMemberPriceNotice = true;
			}
			?>
			@if($end_datetime > now())
				<ul class="event__buy">
					@if($reserved_member == false)
						<li class="event__buy__non-sub-price">
							@lang('main.events.buy.non-sub-price') : {{ $non_sub_price }} $
						</li>
					@endif
					<li class="event__buy__sub-price">
						@lang('main.events.buy.sub-price') : {{ $sub_price }} $
					</li>
					@if($online_register == true)
						<li>
							@lang('main.events.buy.your-price') : {{ $yourPrice }} $
						</li>
					@endif
				</ul>

				@if($online_register == true)
					@if($getMemberPriceNotice)
						<div class="event__buy__online-register">
							<div class="event__buy__notice ">
								@lang('main.events.get_member_price', ['url' => urlRouteName('become-member')])
							</div>
						</div>
					@endif
					<div class="event__buy__button">
						{{ Form::open(['url' => urlRouteName('cart.store'), 'class' => 'form']) }}
						{{ Form::hidden('product_type', $class) }}
						{{ Form::hidden('product_id', $id) }}
						{{ Form::submit(__('main.events.buy_ticket'), ['class' => 'call-to-action', 'style' => 'margin-top: 20px;']) }}
						{{Form::close() }}
					</div>
				@endif
			@endif
			{{-- <div class="event__description">{!! $description !!}</div> --}}
		</div>
	</div>
</section>

{!! $blocs !!}

<section>
	@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
	<div class="event__footer optimal-content-width">
		<a class="see-less" href="{{ urlRouteName('basic-events') }}">@lang('main.back-all-events')</a>
	</div>
</section>

<div class="subscription-single">
	<div class="subscription-single__body">
		<div>
			<h2 class="subscription-single__title">{{ $subscription->title }}</h2>
			<div class="subscription-single__duration">{{ $subscription->duration }} @lang('main.months')</div>
			<div class="subscription-single__cost">{{ $subscription->cost }}$</div>
		</div>
		<div class="spacer"></div>
		@if($subscription->buyable_online == 1)
			{{ Form::open(['url' => urlRouteName('cart.store'), 'class' => 'form']) }}
				{{ Form::hidden('product_type', $subscription->class) }}
				{{ Form::hidden('product_id', $subscription->id) }}
				{{ Form::submit(__('main.purchase'), ['class' => 'call-to-action']) }}
			{{Form::close() }}
		@endif
	</div>
</div>

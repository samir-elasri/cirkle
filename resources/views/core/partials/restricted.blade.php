<section>
	<div class="narrow-content-width">
		<div class="content-card">
			{!! Form::open(['url' => urlRouteName('subscriber.login'), 'class' => 'form']) !!}
				<div class="form__column">
					{!! Form::label('email', trans('auth.register.email')) !!}
					{!! Form::email('email') !!}
				</div>
				<div class="form__column">
					{!! Form::label('password', trans('auth.register.password') )!!}
					{!! Form::password('password') !!}
				</div>
				<div class="form__column">
					{!! Form::submit(__('form.submit'), ['class' => 'call-to-action']) !!}
				</div>
				<div class="restricted__links">
					@if(config('app.allow_user_registration'))
						<a href="{!! urlRouteName('register') !!}">@lang('auth.sign-in-subscriber')</a>
						<a href="{!! urlRouteName('register-supplier-step-1') !!}">@lang('auth.sign-in')</a>
						<a href="{!! urlRouteName('lost-password') !!}">@lang('auth.lost-password')</a>
					@endif
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</section>

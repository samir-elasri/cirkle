<div style="display: contents;" data-component="profile">
	<div class="form" data-ref="profile">

		@if ($subscriber->formatted_member_number)
			<div class="form__column">
				{{ Form::label('member_number', trans('main.memberNumber') . ':') }}
				<span style="font-weight:700;font-size:1.15em;color:#157a47">{{ $subscriber->formatted_member_number }}</span>
			</div>
		@endif

		<div class="form__column">
			{{ Form::label('email', trans('auth.register.email') . ':') }}
			<span>{{ $subscriber->email }}</span>
		</div>

		@if ($subscriber->first_name)
			<div class="form__column">
				{{ Form::label('first_name', trans('auth.register.first_name') . ':') }}
				<span>{{ $subscriber->first_name }}</span>
			</div>
		@endif

		@if ($subscriber->last_name)
			<div class="form__column">
				{{ Form::label('last_name', trans('auth.register.last_name') . ':') }}
				<span>{{ $subscriber->last_name }}</span>
			</div>
		@endif

		@if ($subscriber->company_name)
			<div class="form__column">
				{{ Form::label('company_name', trans('auth.register.company_name') . ':') }}
				<span>{{ $subscriber->company_name }}</span>
			</div>
		@endif

		<div class="form__column ">
			{{ Form::label('preference_language', trans('auth.register.preference_language') . ':') }}
			<span>{{ $subscriber->preference_language_name }}</span>
		</div>
	</div>

	<div class="form__column" data-ref="editPasswordButton">
		<button type="button" class="call-to-action">
			@lang('auth.profile.update-password')
		</button>
	</div>

	{{ Form::open(['url' => urlRouteName('subscriber.update-password'), 'class' => 'form profile-form', 'data-ref'=> 'editPassword', 'style' => 'display: none']) }}

		<div class="form__column " style="margin-top: 20px;">
			{{ Form::label('password', trans('auth.register.password')) }}
			{{ Form::password('password') }}
		</div>

		<div class="form__column ">
			{{ Form::label('password_confirmation', trans('auth.register.password_confirmation')) }}
			{{ Form::password('password_confirmation') }}
		</div>

		<div class="form__row">
			<div class="form__column">
				{{ Form::submit(trans('auth.profile.update-password'), ['class' => 'call-to-action']) }}
			</div>
			<div class="form__column">
				<button type="button" class="call-to-action" data-ref="cancelPasswordButton">
					@lang('main.cancel')
				</button>
			</div>
		</div>
	{{ Form::close() }}


	<div class="form__column">
		<a class="call-to-action" href="{{ urlRouteName('subscriber.logout') }}">
			@lang('auth.profile.logout')
		</a>
	</div>
</div>

{!! $blocs !!}
<section class="recover-password">
	<div class="optimal-content-width">
		@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
		@if(Session::get('success'))
			<a href="{{urlRouteName('profile')}}">@lang('auth.profile.go-to')</a>
		@else
			{{ Form::open(['url' => urlRouteName('subscriber.update-password'), 'class' => 'form']) }}
				<div class="form__column">
					{{ Form::label('password', __('auth.register.password')) }}
					{{ Form::password('password') }}
					@foreach($errors->get('password', '<small style="color: red">:message</small>') as $error)
						{!! $error !!}
					@endforeach
				</div>
				<div class="form__column">
					{{ Form::label('password_confirmation', __('auth.register.password_confirmation')) }}
					{{ Form::password('password_confirmation') }}
					@foreach($errors->get('password_confirmation', '<small style="color: red">:message</small>') as $error)
						{!! $error !!}
					@endforeach
				</div>
				<div class="form__column">
					{{ Form::submit(__('main.submit'), ['class' => 'call-to-action']) }}
				</div>
				{{ Form::hidden('token', Request::get('token')) }}
			{{ Form::close() }}
		@endif
	</div>
</section>

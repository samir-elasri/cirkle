{!! $blocs !!}
<section class="with-background page">
	<div class="with-backgroundpage__content login optimal-content-width">
		@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
		{{-- Bouton retour demandé par Denis sur la page de connexion : revient à la page
		     précédente si possible, sinon à l'accueil. --}}
		<div class="login__back" style="margin-bottom:14px;">
			<a href="{{ urlRouteName('home') }}"
			   class="call-to-action cta-alt"
			   onclick="if(window.history.length>1){history.back();return false;}">← @lang('main.back')</a>
		</div>
		{!! Form::open(['url' => urlRouteName('subscriber.login'), 'class' => 'form']) !!}
		@if(Route::currentRouteName() != "login")
			{!! Form::hidden('current_url', Request::fullUrl()) !!}
		@endif

		<div class="form__column ">
			{!! Form::label('email', __('properties.email') . ' *') !!}
			@foreach($errors->get('email', '<small style="color: red">:message</small>') as $error)
				{!! $error !!}
			@endforeach
			{!! Form::email('email', null, ['class' => 'form-control needed', 'id' => 'email', 'placeholder' => __('properties.email')])!!}
		</div>

		<div class="form__column ">
			{!! Form::label('password', __('properties.password'). ' *') !!}
			@foreach($errors->get('password', '<small style="color: red">:message</small>') as $error)
				{!! $error !!}
			@endforeach
			{!! Form::password('password', ['class' => 'form-control needed', 'id' => 'password', 'placeholder' => __('properties.password')])!!}
		</div>
		<div class="row">
			{!! Form::submit(__('form.submit'), ['class' => 'call-to-action', 'style' => 'margin-bottom:10px;']) !!}
		</div>
		{!! Form::close() !!}

		<div class="login__footer">
			<div>
				<a href="{!! urlRouteName('register-supplier-step-1')!!}">
					@lang('main.register')
				</a>
			</div>
			<div>
				<a href="{!! urlRouteName('lost-password') !!}">
					@lang('main.lost-password')
				</a>
			</div>
		</div>
	</div>
</section>

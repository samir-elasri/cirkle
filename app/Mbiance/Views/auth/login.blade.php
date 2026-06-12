@extends('_layouts.simple')

@section('head')

	@if(config('google.recaptcha.active'))
		<!-- Google reCaptcha -->
		<meta property="recaptcha:site_key" content="{{ config('google.recaptcha.site_key') }}">
		<meta property="recaptcha:input_name" content="{{ config('google.recaptcha.input_name') }}">
		<script type="text/javascript"
				src="//www.google.com/recaptcha/api.js?render={{ config('google.recaptcha.site_key') }}"></script>
	@endif

@endsection

@section('content')

	{!! Form::open(array('route' => 'admin.login.post', 'class' => 'form-horizontal login', 'recaptcha' => false)) !!}

	<div class="login-box">

		<div class="header">
			Connexion
		</div>

		<!-- message box -->
		@include('partials.message')

		<fieldset class="col-sm-12">

			<div class="form-group">
				<div class="controls row">
					<div class="input-group col-sm-12">
						{!! Form::email('email', null, ['class' => 'form-control', 'id' => 'email', 'autocomplete' => 'section-admin username']) !!}
						<span class="input-group-addon"><i class="fa fa-user"></i></span>
					</div>
				</div>
				<div class="controls row">
					@if (!empty($errors))
						{!! $errors->first('email', '<p class="error">:message</p>') !!}
					@endif
				</div>
			</div>

			<div class="form-group has-feedback">
				<div class="controls row">
					<div class="input-group col-sm-12">
						{!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'autocomplete' => 'section-admin current-password']) !!}
						<span class="input-group-addon"><i class="fa fa-key"></i></span>
					</div>
				</div>

				<div class="controls row">
					@if (!empty($errors))
						{!! $errors->first('password', '<p class="error">:message</p>') !!}
					@endif
				</div>
			</div>

			<div class="row">
				{!! Form::submit('se connecter', ['class' => 'btn btn-lg btn-primary col-xs-12']) !!}
			</div>

		</fieldset>
		<div class="clearfix"></div>

	</div>

	{!! Form::close() !!}

@stop

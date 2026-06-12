{!! $blocs !!}
<section>
	<div class="narrow-content-width">
		<div class="content-card">
            {!! Form::open(['url' => urlRouteName('subscriber.lost'), 'class' => 'form']) !!}
                <div class="form__column ">
                    {!! Form::label('email', trans('auth.register.email')) !!}
                    @foreach($errors->get('email', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    {!! Form::email('email')!!}
                </div>
				<div class="form__column">
                    {!! Form::submit(__('form.submit'), ['class' => 'call-to-action', 'style' => 'margin-bottom: 10px;']) !!}
                </div>
				<div class="restricted__links">
                    <a href="{!! urlRouteName('profile') !!}">@lang('auth.login')</a>
				</div>
            {!! Form::close() !!}
        </div>
    </div>
</section>

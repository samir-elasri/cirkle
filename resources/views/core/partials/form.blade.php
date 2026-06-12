@if(isset($form_generator_id))

	@include('partials.flashMessages')

	@if($form_generator_id)
		<div style="margin-bottom:.75rem">@lang('form.asterisk_required')</div>

		<form method="POST" action="{{ urlRouteName('handleForm.post', ['id' => $form_generator_id]) }}#{{ $id }}-target" accept-charset="UTF-8" enctype="multipart/form-data">
			@csrf

			@if(empty($errors->all()))
				{!! App\Models\Core\Forms\FormGenerator::find($form_generator_id)->fieldsHtml(Request::old()) !!}
			@else
				{!! App\Models\Core\Forms\FormGenerator::find($form_generator_id)->fieldsHtml(Request::old(), $errors) !!}
			@endif

			{!! Form::hidden('current_url', Request::fullUrl()) !!}

			@if (!empty($captcha))
				{!! Form::captcha() !!}
				{!! NoCaptcha::renderJs(App::getLocale()) !!}
			@endif

			{!! Form::hidden('bloc_id', $id) !!}

			@if(!empty($call_to_action_label))
				<button type="submit" class="call-to-action" >{!! $call_to_action_label !!}</button>
			@else
				<button type="submit" class="call-to-action" >{!! __('properties.send') !!}</button>
			@endif

		</form>

	@endif

@endif

@extends('core.layouts.bloc', [
	'need_inner_spacing' => $need_inner_spacing || !empty($media_type) && $align == 'back'
])

@section('bloc-content-front')
	<div class="bloc-form__content">
		@if($form = $bloc->blocable->formGenerator)
			<div>
				<div class="description">{!! $content ?? '' !!}</div>
			</div>

			@if($form)
				<div style="margin-bottom:.75rem">@lang('form.asterisk_required')</div>

				{!! Form::open([
					'method' 	=> 'post',
					'url' 		=> urlRouteName('handleForm.post', ['id' => $form_generator_id, '#' => 'target-1']),
					'files' 	=> true,
				]) !!}

				@if(empty($errors->all()))
					{!! $form->fieldsHtml(Request::old()) !!}
				@else
					{!! $form->fieldsHtml(Request::old(), $errors) !!}
				@endif

				{!! Form::hidden('current_url', Request::fullUrl()) !!}

				<br>

				{!! Form::hidden('bloc_id', $id) !!}

					<?php
					$today = new Datetime(Carbon\Carbon::now()->format('Y-m-d\ H:i:s'));
					if ($today < new Datetime($end_datetime) && $today > new Datetime($start_datetime) && $use_activation) {
						$bool = true;
					} elseif (!$use_activation) {
						$bool = true;
					} else {
						$bool = false;
					}
					?>
				@if(!empty($call_to_action_label))
					<button type="submit"
							class="call-to-action" {!! $bool ? '' : 'disabled style="background-color:grey"' !!}>{!! $call_to_action_label !!}</button>
				@else
					<button type="submit"
							class="call-to-action" {!! $bool ? '' : 'disabled style="background-color:grey"' !!}>{!! __('properties.send') !!}</button>
				@endif

				{!! Form::close() !!}

			@endif
		@endif
	</div>
@stop

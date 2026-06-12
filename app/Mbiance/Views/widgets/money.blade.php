<div id="{{$field}}_group" class="form-group {{ $isRequired ? ' has-feedback' : '' }} @if ($errors->has($field)){{ $errors->first($field, 'has-error') }}@endif ">

	<label for="site_id" class="col-md-3 control-label">{{ $label }} : <span class="sr-only">requis</span></label>
	<div class="col-md-9">

		<div class="input-prepend input-group col-sm-3">
			<span class="input-group-addon"><i class="fa fa-dollar"></i></span>
			<?php $extraClasses = ($field == 'total_damages_price') ? $obj->moneyFieldExtraCssClasses() : ''; ?>
			{!! Form::text($field, Request::get($field, number_format($value, 2)), ['class' => 'form-control ' . $extraClasses, Arr::get($options, 'readonly') == true ? 'readonly' : '']) !!}
		</div>

	</div>
</div>
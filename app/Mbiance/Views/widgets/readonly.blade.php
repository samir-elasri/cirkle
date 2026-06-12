<div id="{{$field}}_group" class="form-group {{ $isRequired ? ' has-feedback' : '' }} @if ($errors->has($field)){{ $errors->first($field, 'has-error') }}@endif ">

	<label for="site_id" class="col-md-3 control-label">{{ $label }} : <span class="sr-only">requis</span></label>
	<div class="col-md-9">
		 {!! Form::text($field, null, ['class' => 'form-control', 'readonly']) !!}
	</div>
</div>
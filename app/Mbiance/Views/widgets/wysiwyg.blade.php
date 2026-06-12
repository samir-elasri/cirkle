<div id="{{ $field }}_group" {!! !empty($toggle) ? "data-toggle=\"{$toggle}\"" : '' !!}  class="form-group {{ $isRequired ? ' has-feedback' : '' }} @if ($errors->has($field)){{ $errors->first($field, 'has-error') }}@endif ">
    <label for="{{ $field }}" class="col-md-3 control-label">{{ ($isRequired ? '<i class="fa fa-asterisk"></i> ' : '') . $label }}:</label>
    <div class="col-md-9">
        {!! Form::textarea($field, $value, ['data-tinymce' => ''] + $options) !!}
    </div>
</div>

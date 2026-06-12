<?php
/** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany $relation */
/** @var string $field */
/** @var \App\Models\Core\Model $obj */
/** @var array $options */
$relationName = $options['relation'];
$relation = $obj->{$relationName}();

$key = $relation->getQualifiedRelatedKeyName();
$selected = $relation->pluck($key);
$selected = $selected->count() ? $selected->implode(',') : null;
?>

<div data-ng-controller="AssociateCtrl" {!! !empty($toggle) ? 'data-toggle="' . $toggle . '"' : '' !!} data-ng-cloak
	 data-ng-init="selected='{{ $selected }}';field='{{ $field }}';">

	<div id="{{ $field }}_group"
		 data-ng-init='collection={!! $options['associate_class']::get()->toJson(JSON_HEX_APOS) !!}'
		 class="form-group {{ $isRequired ? ' has-feedback' : '' }} @if ($errors->has($field)) {{ $errors->first($field, 'has-error') }} @endif ">

		<label for="<% field %>" class="col-md-3 control-label">{{ $label }}
			@if ($isRequired)
				<span class="sr-only"> {{ __('form.required') }}</span>
			@endif
			:</label>

		<div class="col-md-9">
			<tag-editor field="{{ $field }}" selected="selected" collection="collection"
						required="{{ $isRequired ? 'true' : 'false' }}"></tag-editor>
		</div>
	</div>

</div>

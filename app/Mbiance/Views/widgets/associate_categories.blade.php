<?php
	$selected = $obj->getAssociateCategories($field, true);
	$selected = !empty($selected) ? implode(',', $selected) : null;
?>

<div data-ng-controller="AssociateCtrl" {!! !empty($toggle) ? 'data-toggle="' . $toggle . '"' : '' !!} data-ng-cloak data-ng-init="selected='{{ $selected }}';field='{{ $field }}';">
	<div id="{{ $field }}_group" data-ng-init='collection={{ App\Models\Core\Category::getListByIdentifier($options["identifier"])->toJson(JSON_HEX_APOS) }}' class="form-group {{ $isRequired ? ' has-feedback' : '' }} @if ($errors->has($field)) {{ $errors->first($field, 'has-error') }} @endif ">
		<label for="<% field %>" class="col-md-3 control-label">{{ $label }}
			@if ($isRequired)
			 <span class="sr-only"> {{ __('form.required') }}</span>
			@endif
		:</label>
		<div class="col-md-9">
 			<tag-editor field="{{ $field }}" selected="selected" collection="collection" required="{{ $isRequired ? 'true' : 'false' }}"></tag-editor>
		</div>
	</div>
</div>

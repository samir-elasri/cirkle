<div data-ng-controller="AssociateCtrl" data-ng-cloak data-ng-init="selected='{{ $obj->$field }}';field='{{ $field }}';">
	<div class="form-group" data-ng-init='collection={!! $options['associate_class']::get()->toJson(JSON_HEX_APOS) !!}'>
		<label for="<% field %>" class="col-md-3 control-label">{{ $label }} :</label>
		<tag-grid field="{{$field}}" selected="selected" collection="collection"></tag-grid>
	</div>
</div>
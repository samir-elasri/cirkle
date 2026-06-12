@php
	$categories = \App\Models\Core\Category::where("category_group_id", $options["category_group_id"])->where("active", true)->get();

	if(!empty($obj->$field)) {
		$ids = explode(',', $obj->$field);
		$selectedCategories = \App\Models\Core\Category::findMany($ids);
		$categories = $categories->merge($selectedCategories);
	}
@endphp

<div data-ng-controller="AssociateCtrl" data-ng-cloak data-ng-init="selected='{{ $obj->$field }}';field='{{ $field }}';">
	<div class="form-group" data-ng-init='collection={{ $categories }}'>
		<label for="<% field %>" class="col-md-3 control-label">{{ $label }} :</label>
		<div class="col-md-9">
 			<tag-editor field="{{$field}}" selected="selected" collection="collection"></tag-editor>
		</div>
	</div>
</div>
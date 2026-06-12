@extends('_layouts.component.grid')

@section('content')

	<div class="panel-body" data-ng-controller="GridCtrl">

		{!! GridUtility::addItem() !!}

        <?php
        $className = ModelUtility::getClassNameFromRoute();
        $collectionName = ModelUtility::getCollectionNameFromRoute();
        ?>

		<script>
			var info = { relation: '', fkey: '', collection: '{{ $collectionName }}', id: 0 };
			var collections = {!! class_exists($className) ? $className::getGridList() : '[]' !!};
		</script>

		<table class="table table-bordered table-striped table-condensed" id="grid">
			<thead>
			<tr>
				{!! GridUtility::getHeaderFields(true) !!}
				<th></th>
				<th class="col-xs-2">Actions</th>

			</tr>
			</thead>

			<tbody data-as-sortable="orderSortOptions" data-ng-model="collections">

			<tr data-ng-repeat="item in collections" data-as-sortable-item>

				{!! GridUtility::setValueFields() !!}

				<td style="width:50px">
					<div class="td-order" style="width:50px">

						<a class="btn btn-desc btn-success btn-xs" data-ng-show="!$last"
						   data-ng-click="setOrder('desc',$index);"><i class="fa fa-sort-desc"></i></a>
						<a class="btn btn-asc btn-success btn-xs" data-ng-show="!$first"
						   data-ng-click="setOrder('asc' ,$index);"><i class="fa fa-sort-asc"></i></a>
					</div>
				</td>

				<td>

					@if (GridUtility::isHeadline())
						<a class="btn btn-success" data-ng-hide="item.is_headline" data-ng-click="setHeadline(item.id)"><i
									class="fa fa-check"></i></a>
					@endif

					<a href="/admin/{{ $collectionName }}/<% item.id %>/edit" class="btn btn-info"><i
								class="fa fa-edit"></i></a>
					<btn-confirm
							{{ $collectionName == 'categories' ? 'data-ng-if="item.identifier == \'\'"' : '' }} title="Supprimer"
							url="/admin/{{ $collectionName }}/" id="item.id"></btn-confirm>

				</td>

			</tr>
			</tbody>
		</table>


	</div>

	@include('partials.confirmModal')

@stop

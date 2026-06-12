@if(!$isCreate)

	<?php
		$collectionName = ModelUtility::getChildrenCollectionNameFromRoute();
	?>

	<div class="panel-body" style="padding: 0" data-ng-controller="GridCtrl">

		@if ($collectionName === 'blocs')
			<?php $collection_name = ModelUtility::getCollectionNameFromRoute() ?>

			<div id="addElementSection" class="iconOption btn-group" >
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
					<i class='fa fa-caret-down'></i> Ajouter un bloc
				</button>

				<ul class="dropdown-menu" role="menu">
					@foreach (App\Models\Core\Bloc::getEnum() as $enum => $key)
						<li><a href="{{ adminRouteName(Route::currentRouteName(), [Request::segment(3), 'onglet' => 'blocs', 'childId' => 'create', 'bloc_type' => $enum]) }}">{{ $key }}</a></li>
					@endforeach
				</ul>
			</div>

		@else

			<p id="addElementSection" class="iconOption" style="display: flex; flex-direction: row">
				{!! PageUtility::addItemLink() !!}
				{!! PageUtility::addExport() !!}
			</p>

		@endif

		<?php
			$headerFields = GridUtility::getHeaderFields();
			$temp = GridUtility::getModel();

	        $order_index = array_search($temp->order_default, $temp['grid']);
		?>

		<table data-model="{{ get_class($temp) }}" data-sort="{{ Session::get('grid-state.data.sortIndex') ?? $order_index }}" data-order="{{ Session::get('grid-state.data.order') ?? $temp->order_direction }}" data-sort-order="{{$temp->order_direction}}" class="table table-bordered table-striped table-condensed {{ GridUtility::isManageOrdre() ? '' : 'bootstrap-datatable datatable' }}" id="grid" data-order-index="{{ $order_index ?: 0 }}">			<thead>
			<tr>
				{!! $headerFields !!}

				@if (GridUtility::isManageOrdre())
					<th></th>
				@endif
				<th class="col-xs-2">Actions</th>
			</tr>
			</thead>

			@if (GridUtility::isManageOrdre())

				<script>
					var info = { relation: '{!! ModelUtility::getCollectionNameFromRoute() !!}', fkey:'{!! $model->getForeignKey() !!}', collection: '{!! Str::snake($collectionName) !!}', id: {!! $model->id !!} };
					var collections = {!! FormUtility::getCollectionForSubgrid() !!};
				</script>

				<tbody data-as-sortable="orderSortOptions" data-ng-model="collections">

				<tr data-ng-repeat="item in collections" data-as-sortable-item>

					{!! GridUtility::setValueFields() !!}

					@if (GridUtility::isManageOrdre())
						<td style="width:50px">
							<div class="td-order" style="width:50px">
								<a class="btn btn-desc btn-success btn-xs" data-ng-show="!$last" data-ng-click="setOrder('desc',$index);"><i class="fa fa-sort-desc"></i></a>
								<a class="btn btn-asc btn-success btn-xs" data-ng-show="!$first" data-ng-click="setOrder('asc' ,$index);"><i class="fa fa-sort-asc"></i></a>
							</div>
						</td>
					@endif

					<td>
						<a ng-show="item.blocable_type" href="<% item.url %>" target="_blank" class="btn btn-success"><i class="fa fa-external-link"></i></a>

						@if (GridUtility::isHeadline())
							<a class="btn btn-success" data-ng-hide="item.is_headline" data-ng-click="setHeadline(item.id)" style="margin: 1px 2px;" title="{{ Request::segment(2) == 'reservations' ? 'Identifier comme conducteur principal' : 'mettre à la une' }}"><i class="fa fa-check"></i></a>
						@endif
						<a href="{!! Request::fullUrl() !!}<% item.blocable_type ?  '/' + item.blocable_id + '?bloc_type=' + item.bloc_collection  : '/' + item.id %>" class="btn btn-info" style="margin: 1px 2px;" title="Éditer"><i class="fa fa-edit"></i></a>

						@if($collectionName == 'categories')
							<btn-confirm data-ng-if="item.identifier == '' || item.identifier === null" title="Supprimer" url="/admin/{{ Str::snake($collectionName) }}/" id="item.id"></btn-confirm>
						@else
							<btn-confirm title="Supprimer" url="/admin/{{ Str::snake($collectionName) }}/" id="item.id"></btn-confirm>
						@endif

					</td>

				</tr>
				</tbody>

			@else
				<script>
					var info = {};
					var collections = [];
				</script>

				{!! GridUtility::getData([$model::find(Request::segment(3)), $collectionName]) !!}
			@endif

		</table>
	</div>

	@if (!empty(Session::get('grid-state')))
		<script id="grid-state" type="application/json">
			{!! json_encode(Session::get('grid-state')) !!}
		</script>
	@endif

	@include('partials.confirmModal')

@endif

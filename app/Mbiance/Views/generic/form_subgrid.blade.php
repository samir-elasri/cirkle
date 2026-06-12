	@if(ModelUtility::getChildrenCollectionNameFromRoute() === 'blocs')

		<?php $collection_name = ModelUtility::getCollectionNameFromRoute() ?>
		<div class="panel-body" style="padding: 0 0 15px 0">
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
			<a class="btn btn-inverse" href="{{ adminRouteName(Route::currentRouteName(), [Request::segment(3), 'onglet' => 'blocs']) }}"><i class='fa fa-chevron-left'></i> Retour à la liste des blocs</a>
		</div>

	@else

		<div class="panel-body" style="padding: 0 0 15px 0">

			{!! PageUtility::addItemLink() !!}

			{!! PageUtility::backToListLink() !!}

		</div>

	@endif

	{!! FormUtility::open() !!}

	{!! FormUtility::notice() !!}

	{!! FormUtility::generateForm() !!}

	@if(ModelUtility::getChildrenCollectionNameFromRoute() === 'blocs')

		{!! FormUtility::relation('pageable_id') !!}

		{!! Form::hidden('pageable_type', FormUtility::getPageableType()) !!}

		{!! Form::hidden('blocable_type', FormUtility::getBlocableType()) !!}

	@endif

	{!! Form::hidden('current_url', Request::fullUrl()) !!}

	{!! FormUtility::saveBtn() !!}

	{!! FormUtility::close() !!}

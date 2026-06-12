@extends('_layouts.admin')

@section('sec-content')

	<h1>{{ GridUtility::getManagingMessage($entite) }}</h1>
	
	<div class="row">		
		<div class="col-lg-12">
			<div class="panel panel-default">
					
				<div class="panel-heading">
					<h2><i class="fa {{ $entite->icone }}"></i><span class="break"></span>{{ GridUtility::getListingMessage($entite) }}</h2>
					<div class="panel-actions"></div>
				</div>

				<div class="panel-body">

					@yield('content')

				</div>
			
			</div>
		</div>
	</div>

@stop
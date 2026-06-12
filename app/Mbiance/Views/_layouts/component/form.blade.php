@extends('_layouts.admin')

@section('sec-content')

	<h1>{{ GridUtility::getManagingMessage($entite) }}</h1>

	<div class="panel panel-default">

        <div class="panel-heading">
	        <h2><i class="fa {{ $entite->icone }}"></i><span class="break"></span> @if($isCreate) {{__('admin.add')}}  @else {{__('admin.edit')}} : @yield('identifiant') @endif</h2>
            <div class="panel-actions"></div>

			@include('partials.onglet')

        </div>

		<div class="panel-body">
			@if((!$isCreate) && ((int)$entite->isPermissionCreate))
				<a href="{{ adminRouteName("admin.$entite->collection.create") }}" id="addElementSection" class="btn btn-sm btn-primary" runat="server" title="{{__('admin.additem')}}"><i class="fa fa-plus"></i> {{__('admin.additem', ['attr' => $entite->Singulier])}}</a>
			@endif

			@if(!empty($entite->Regroupement))
				<a href="{{ adminRouteName("admin.$entite->collection.index") }}" class="btn btn btn-sm btn-inverse" runat="server" title="{{__('admin.backtolist')}}"><i class="fa fa-chevron-left"></i> {{__('admin.backtolist' , ['det' => isset($entite->Determinant) ? $entite->Determinant : __('admin.det'), 'attr' => $entite->Regroupement])}}</a>
			@endif
		</div>

		<div class="panel-body">
			@yield('content')
		</div>

	</div>

@stop

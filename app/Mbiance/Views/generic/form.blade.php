@extends('_layouts.component.form')

@section('identifiant')

	{!! FormUtility::identifiant() !!}

@stop

@section('content')
	
	{!! FormUtility::validationSummary() !!}
    @include(PageUtility::getOnglet()) 

@stop
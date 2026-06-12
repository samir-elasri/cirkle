@extends('_layouts.component.grid')

@section('content')

	{!! GridUtility::showGrid() !!}
	@if (!empty(Session::get('grid-state')))
		<script id="grid-state" type="application/json">
			{!! json_encode(Session::get('grid-state')) !!}
		</script>
	@endif
	@include('partials.confirmModal')

@stop

@extends('_layouts._main')

@section('header')
    @include('partials.header')
@stop

@section('main-content')

	<div class="row-fluid" style="flex-grow: 1">

		@include('partials.sidemenu')

		<!-- start: Content -->
		@if(Request::segment(3) == 'doc')
			<div class="main doc">
		@else
			<div class="main">
		@endif

			@include('partials.message')

			@yield('sec-content')

		</div>
		<!-- end: Content -->

	</div>

	@include('partials.confirmModal')

@stop

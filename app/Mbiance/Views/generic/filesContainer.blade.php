@extends('_layouts.admin')

@section('sec-content')
	
    <iframe src="{{ URL::route('elfinder.index') }}" style="min-height: 600px; width: 100%;" frameborder="0"></iframe>
@stop

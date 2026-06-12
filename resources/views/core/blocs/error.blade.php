@extends('core.layouts.bloc')

@section('bloc-content-front')
	<div class="bloc-error">
		@lang('main.bloc-error')
		<pre class="debug-error">{!! $error ?? '' !!};</pre>
	</div>
@stop
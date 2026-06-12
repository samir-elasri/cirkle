@extends('core.layouts.bloc', ['wait-ready' => true])

@section('bloc-content-front')

	<div class="toc">
		@if($firstGroup = ($data[0] ?? false))
			@foreach($firstGroup as $datum)
				<div class="toc__element" style="grid-column: 1; grid-row: {{$loop->iteration}}">
					 <a href="#{{$datum['target']}}">{{$datum['title']}}</a>
				</div>
			@endforeach
		@endif
		@if($secondGroup = ($data[1] ?? false))
			@foreach($secondGroup as $datum)
				<div class="toc__element" style="grid-column: 2; grid-row: {{$loop->iteration}}">
					<a href="#{{$datum['target']}}">{{$datum['title']}}</a>
				</div>
			@endforeach
		@endif
	</div>
@stop

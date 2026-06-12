@if(Session::get('success'))
	<div data-component='alert' class="alert-success">
		<span class="alert__times">&times;</span>
		{{ Session::get('success') }}
	</div>
@elseif(Session::has('errors'))
	@if (is_string($errors))
		<div data-component='alert' class="alert-error">
			<span class="alert__times">&times;</span>
			{{ Session::get('errors') }}
		</div>
	@else
		@foreach ($errors->toArray() as $field => $error)
		<div data-component='alert' class="alert-error">
			<span class="alert__times">&times;</span>
			{{ $error[0] }}
		</div>
		@endforeach
	@endif
@endif
@if (Session::has('error'))
	<div data-component='alert' class="alert-error">
		<span class="alert__times">&times;</span>
		{{ Session::get('error') }}
		<?php Session::forget('error') ?>
	</div>
@endif
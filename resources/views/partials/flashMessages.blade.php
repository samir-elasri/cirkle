<?php
if (isset($namespace)) {
	$successName = $namespace . '_success';
	$errorName = $namespace . '_error';
	$infoName = $namespace . '_info';
	$warningName = $namespace . '_warning';
} else {
	$successName = 'success';
	$errorName = 'error';
	$infoName = 'info';
	$warningName = 'warning';
}
//echo '$errorName :' .$errorName ;
?>

@if(Session::has($successName) || Session::has($errorName) || Session::has($infoName) || Session::has($warningName))
	<section>
		<div class="optimal-content-width">
			@if(Session::has($successName))
				<div data-component='alert' class="alert alert-success">
					<span class="alert__times">&times;</span>
					{!! Session::get($successName) !!}
				</div>
			@endif

			@if (Session::has($errorName))
				<div data-component='alert' class="alert alert-error">
					<span class="alert__times">&times;</span>
					{!! Session::get($errorName) !!}
					<?php Session::forget($errorName) ?>
				</div>
			@endif

			@if(Session::has($infoName))
				<div data-component='alert' class="alert alert-info">
					<span class="alert__times">&times;</span>
					{!! Session::get($infoName) !!}
				</div>
			@endif

			@if(Session::has($warningName))
				<div data-component='alert' class="alert alert-warning">
					<span class="alert__times">&times;</span>
					{!! Session::get($warningName) !!}
				</div>
			@endif
		</div>
	</section>
@endif

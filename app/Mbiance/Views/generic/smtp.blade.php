@extends('_layouts.admin')

@section('sec-content')
	<div class="panel-body" data-ng-controller="GridCtrl">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2><i class="fa fa-language"></i><span class="break"></span>
					Configuration SMTP
				</h2>
				<div class="panel-actions"></div>
			</div>
			<div class="panel-body">
				<form method="POST" action="{{ urlRouteName('admin.smtp') }}">
					@csrf
					<h2 style="border-top: none;">Configuration SMTP</h2>
					@foreach ($smtp as $index => $value)
						<label for="{{ $index }}" class="col-md-3 control-label">{{ $index }} <span class="sr-only">optionnel</span>:</label>
						<div class="col-md-9" style="margin-bottom: 20px;">

							@if(is_bool($value))
								<input type="hidden" name="{{ $index }}" value="0">
								<input type="checkbox" name="{{ $index }}" {{$value ? 'checked' : ''}} value="1">

							@else
								<input class="form-control" name="{{ $index }}"
									   type="text" value="{{ $value }}">
							@endif

						</div>
					@endforeach
					<div class="col-md-offset-3">
						<button type="submit" class="btn btn-primary">Sauvegarder</button>
					</div>
				</form>
			</div>
		</div>
@stop

@php use Illuminate\Foundation\Application; @endphp
@extends('_layouts.admin')

@section('sec-content')

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2><i class="fa fa-wrench"></i>Configuration pour cet environnement</h2>
					<div class="panel-actions">
						<a href="#" class="btn-minimize"><i class="fa fa-chevron-up"></i></a>
					</div>
				</div>
				<div class="panel-body">
					<table class="table bootstrap-datatablesmall-font no-footer">
						<thead>
						<tr role="row">
							<th>Paramètre</th>
							<th>Valeur</th>
						</tr>
						</thead>
						<tbody>

						@if (Auth::guard('users')->user()->is_mbiance)

							<tr>
								<td>Version du gestionnaire</td>
								<td>{{ config('app.version') }}</td>
							</tr>
							<tr>
								<td>Version Laravel</td>
								<td>{{ Application::VERSION  }}</td>
							</tr>
							<tr>
								<td>Version PHP</td>
								<td>{{ PHP_VERSION  }}</td>
							</tr>
							<tr>
								<td>Profil de l'environnement</td>
								<td>{{ App::environment() }}</td>
							</tr>
							<tr>
								<td>Base de données</td>
								<td>{{ config('database.connections.mysql.database') }}</td>
							</tr>
							<tr>
								<td>Debug mode</td>
								@if(is_admin(true))
									<td>
										<a href="/admin/changeDebug/{{env('APP_DEBUG') ? 'false' : 'true'}}">{{ env('APP_DEBUG') ? 'true' : 'false' }}</a>
									</td>
								@else
									<td>{{ env('APP_DEBUG') ? 'true' : 'false' }}</td>
								@endif
							</tr>
							<tr>
								<td>Caching</td>
								<td>{{ config('app.isCache') === 1 ? 'true' : 'false' }}</td>
							</tr>
						@endif

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2><i class="fa fa-wrench"></i>Import</h2>
					<div class="panel-actions">
						<a href="#" class="btn-minimize"><i class="fa fa-chevron-up"></i></a>
					</div>
				</div>
				<div class="panel-body">
					<form action="{{ route('admin.excel.import') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="col-md-12">
							<label for="file" class="form-label">Choisir un fichier Excel</label>
						</div>
						<div class="col-md-4">
							<input type="file" name="file" id="file" class="form-control" accept=".xlsx, .xls, .csv" required>
						</div>
						<div class="col-md-1">
							<button type="submit" class="btn btn-primary">Envoyer</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2><i class="fa fa-wrench"></i>Statistiques</h2>
					<div class="panel-actions">
						<a href="#" class="btn-minimize"><i class="fa fa-chevron-up"></i></a>
					</div>
				</div>
				<div class="panel-body">
					<table class="table bootstrap-datatablesmall-font no-footer">
						<thead>
						<tr role="row">
							<th>Élément</th>
							<th>Actifs</th>
							<th>Inactifs</th>
							<th>Total</th>
						</tr>
						</thead>
						<tbody>
						@foreach($models as $model)
							<tr>
								<td>{{ $model->name }}</td>
								<td>{{ $model->actives }}</td>
								<td>{{ $model->inactives }}</td>
								<td>{{ $model->total }}</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

@stop

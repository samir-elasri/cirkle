@extends('_layouts.admin')

@section('sec-content')
	<div class="panel-body" style="background-color: white">
		<h2> Groupes de traductions</h2>
		<table class="table table-bordered table-striped table-condensed" id="grid">
			<thead>
				<tr>
					<th>Fichier</th>
					<th class="col-xs-2">Actions</th>
				</tr>
			</thead>
			<tbody>
				@foreach(app('translator')->getAllGroups() as $group)
					<tr>
						<td>
							{{ $group }}
						</td>
						<td>
							<a href="/admin/translations/{{ $group }}/edit" class="btn btn-info"><i class="fa fa-edit"></i></a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@stop

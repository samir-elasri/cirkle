<div id="{{$field}}_group" class="form-group {{ $isRequired ? ' has-feedback' : '' }} @if ($errors->has($field)){{ $errors->first($field, 'has-error') }}@endif ">

	<label for="site_id" class="col-md-3 control-label">{{ $label }} : <span class="sr-only">requis</span></label>
	<div class="col-md-9">
		<table class="table table-striped table-bordered no-footer">
			<thead>
				<tr>
					<th>Titre</th>
					<th>Réponses</th>
				</tr>
			</thead>
			<tbody>
				@foreach($value as $answer)
				<tr class="odd">
					<td>
						{{ $answer->title }}
					</td>
					<td>
						{{ $answer->value }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@extends('_layouts.admin')

@section('sec-content')

	@php
		$group = Route::input('group');
	@endphp

	<div class="panel-body">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2><i class="fa fa-language"></i><span class="break"></span>
					Éditer : traductions groupe {{ $group }}
				</h2>
				<div class="panel-actions"></div>
			</div>
			<div class="panel-body">
				<form id="form" method="POST" action="/admin/translations/{{ $group }}">
					@csrf
					<h2 style="border-top: none;">Traductions du groupe {{ $group }}</h2>
					@foreach(app('translator')->getGroupTranslations($group) as $key => $values)
						<label class="col-md-3 control-label">{{ $key }}</label>
						<div class="col-md-9" style="margin-bottom: 20px;">
							@foreach (getLocales() as $locale)
								<label for="{{ $key . '.' . $locale }}"
									   class="col-sm-1 control-label">{{ $locale }}&nbsp;:</label>
								<div class="col-sm-11" style="margin-bottom: 5px;">
									<input class="form-control"
										   id="{{ $key . '.' . $locale }}"
										   name="{{ $locale . '[' . str_replace('.', '][', $key) . ']' }}"
										   type="text"
										   placeholder="{{ $values[$locale][0] ?? '' }}"
										   value="{{ $values[$locale][1] ?? '' }}"
										   onfocus="this.value = !this.value ? this.placeholder : this.value"
										   onblur="this.value = this.value === this.placeholder ? '' : this.value"
									>
								</div>
							@endforeach
						</div>
					@endforeach
					<div class="col-md-offset-3">
						<div class="col-sm-offset-1">
							<button type="submit" class="btn btn-primary">Sauvegarder</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script type="application/javascript">
		const form = document.getElementById('form');

		const submitForm = (e) => {
			e.preventDefault();
			
			// Get all inputs
			const inputs = document.querySelectorAll("input[type='text']");
			
			inputs.forEach((el) => {
				// If the value equals the placeholder, the user wants to use the base translation
				// So we clear the value to indicate no override
				if (el.value === el.placeholder) {
					el.value = '';
				}
				// Don't disable any inputs - we want all fields submitted
				// Empty values mean "no override, use base translation"
			});

			document.getElementById('form').submit();
		};

		form.addEventListener('submit', submitForm);
	</script>
@stop

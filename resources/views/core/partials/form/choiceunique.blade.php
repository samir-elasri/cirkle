<div class='field Generated-field'>
	<div class='field-label has-text-left padding-bottom-5'>
        <label class='label' for="{{ $name }}">{{ $label }}{{ $requiredString }}</label>

		@if($options['explanations'])
        <div class='Generated-popper'>
			<div class='Popper-btn'>&nbsp;</div>

            <p class='Popper-content'>{{ $options['explanations'] }}</p>
		</div>
		@endif
	</div>

    <div class='field-body'>
		<div class='field'>
			<div class='control'>
				<div class='select is-fullwidth {{ $errorClass }}'>
                    <select name='{{ $name }}' id="{{ $name }}" {{ !empty($requiredString) ? 'aria-required="true"' : '' }} {{ !empty($errorMessage) ? "aria-describedby='error-" . $name . "'" : '' }}>
						<option value=''>
							{{ __('properties.please_select') }}
						</option>
						@foreach($options['options'] as $option)
							<option value="{{ $option['code_value'] }}" {{ $option['other'] ? 'other-choice' : '' }} {{ $option['selected'] ? 'selected' : '' }}>
								{{ $option['title'] }}
							</option>
						@endforeach
					</select>
				</div>
				{!! $errorMessage !!}
			</div>
		</div>
	</div>
</div>

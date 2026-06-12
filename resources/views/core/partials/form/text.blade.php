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
				<textarea
					{{ !empty($options['max_chars']) ? 'maxlength="' . $options['max_chars'] . '"' : '' }}
					class='textarea {{ $errorClass }}'
					placeholder=''
					{{ !empty($requiredString) ? 'aria-required="true"' : '' }}
					rows='5'
					name='{{$name}}'
                    {{ !empty($errorMessage) ? "aria-describedby='error-" . $name . "'" : "aria-describedby='max-" . $name . "'" }}
                    id="{{ $name }}"
					data-max-chars='{{ $options['max_chars'] }}'>{{ $value }}</textarea>
					@if($options['max_chars'] > 0)
						<p class='pull-right is-size-7 Chars-counter' id="max-{{ $name }}"><span >{{ $options['max_chars'] }}</span> {{ __('properties.remaining_characters') }}.</p>
					@endif
				{!! $errorMessage !!}
			</div>
		</div>
	</div>
</div>

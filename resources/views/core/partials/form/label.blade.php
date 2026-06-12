<?php
	$max_char = \Arr::get($options, 'max_chars', 0);
	$is_other = \Arr::get($options, 'is_other', false);
	$explanations = \Arr::get($options, 'explanations', false);
?>
<div class='field Generated-field' {{ $is_other ? 'other-choice=\''.$name.'\'' : ''}}>
	<div class='field-label has-text-left padding-bottom-5'>
        <label class='label' for="{{ $name }}" {{ !empty($requiredString) ? 'area-label="Champs requis"' : '' }}>
			{{ $label }}{{ $requiredString }}
		</label>
		@if($explanations)
        <div class='Generated-popper'>
			<div class='Popper-btn'>&nbsp;</div>

            <p class='Popper-content'>{{ $explanations }}</p>
		</div>
		@endif
	</div>

    <div class='field-body'>
		<div class='field'>
			<div class='control'>
				<input
					{{ !empty($options['max_chars']) ? 'maxlength="' . $options['max_chars'] . '"' : '' }}
					value='{{ $is_other ? Request::old('other-choice-' . $name) : $value }}'
					class='input {{ $errorClass }}'
					type='text'
					placeholder=''
					{{ !empty($requiredString) ? 'aria-required="true"' : '' }}
					id="{{ $name }}"
					{{ !empty($errorMessage) ? "aria-describedby='error-" . $name . "'" : '' }}
					name='{{ $name }}'
					data-max-chars='{{ $max_char <= 0 ? 255 : $max_char }}'>
					@if($max_char > 0)
						<p class='pull-right is-size-7 Chars-counter'><span>{{ $max_char }}</span> {{ __('properties.remaining_characters') }}.</p>
					@endif
					{!! $errorMessage !!}
			</div>
		</div>
	</div>
</div>

<div class='field Generated-field'>
	<div class='field-label has-text-left padding-bottom-5'>
		<label class='label'>{{ $label }}{{ $requiredString }}</label>

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
				<div class='file has-name is-fullwidth {{ $errorClass }}'>
					<label class='file-label'>
						<input class='file-input' type='file' name='{{$name}}_file'>
						<span class='file-cta'>
						<span class='file-icon'>
							<i class='fa fa-upload'></i>
						</span>
						<span class='file-label'>
							{{ __('properties.select_a_file') }}
						</span>
						</span>
						<span class='file-name'>
						</span>
					</label>
				</div>
				{!!$errorMessage!!}
			</div>
		</div>
	</div>
</div>

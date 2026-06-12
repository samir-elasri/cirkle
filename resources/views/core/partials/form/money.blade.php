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
			<div class='control has-icons-left'>
				<input
					value='{{$value}}'
					class='input mask-is-money has-text-left {{$errorClass}}'
					type='text'
					id="{{ $name }}"
					placeholder=''
					{{ !empty($errorMessage) ? "aria-describedby='error-" . $name . "'" : '' }}
                    {{ !empty($requiredString) ? 'aria-required="true"' : '' }}
					name='{{$name}}'>
					<span class='icon is-small is-left'>
						<i class='fa fa-usd'></i>
					</span>
				{!!$errorMessage!!}
			</div>
		</div>
	</div>
</div>
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
                <input id="{{ $name }}" value='{{$value}}' {{ !empty($errorMessage) ? "aria-describedby='error-" . $name . "'" : '' }} {{ !empty($requiredString) ? 'aria-required="true"' : '' }} class='input mask-is-phone {{$errorClass}}' type='tel' placeholder='' name='{{$name}}'>
				<span class='icon is-small is-right'>
					<i class='fa fa-phone'></i>
				</span>
				{!!$errorMessage!!}
			</div>
		</div>
	</div>
</div>
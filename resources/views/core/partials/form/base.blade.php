<div class='field Generated-field'>
	<div class='field-label has-text-left padding-bottom-5'>
		<label class='label'>{{ $label }}{{ $requiredString }}</label>
        
        <div class='Generated-popper'>
			<div class='Popper-btn'>&nbsp;</div>
            
            <p class='Popper-content'>{{ $options['explanations'] }}</p>
		</div>
	</div>
    
    <div class='field-body'>
		<div class='field'>
			@yield('field')
		</div>
	</div>
</div>
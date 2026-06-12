<div class='field Generated-field'>
	<div class='field-label has-text-left padding-bottom-5'>
        <label class='label' for="{{ $name }}" {{ !empty($requiredString) ? 'area-label="Champs requis"' : '' }}>{{ $label }}{{ $requiredString }}</label>
		
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
				<div class="select-multiple is-fullwidth {{$errorClass}}">
					@foreach($options['options'] as $option)
					<label>
                        <input id="{{ $name }}" class="checkbox" type="checkbox" name='{{$name}}[]' value="{{ $option['code_value'] }}" {{ $option['other'] ? 'other-choice' : '' }} {{ $option['selected'] ? 'checked' : '' }}/> 
						{{ $option['title'] }}
					</label>
					@endforeach
				</div>
				{!!$errorMessage!!}
			</div>
		</div>
	</div>
</div>
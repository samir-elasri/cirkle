<div class="single
			{{ $align == 'back' ? 'single--' . (empty($text) ? 'no-' : '') . 'flip' : '' }}"

	 style="max-width: {{ $width }}px"
>

	@if($align === 'back')

		@if(empty($call_to_action_label))
		<a href="{{ $call_to_action_url }}">
		@endif

		<div class="single-front" style="background-size: {{ $group->image_mode }}; background-image: url({{ $image }});">

			<div class="single-front-titles">
				@if(!empty($title))
					<h3 class="single-front-title">
						{{ $title }}
					</h3>
				@endif

				@if(!empty($sub_title))
					<h4 class="single-front-sub-title">
						{{ $sub_title }}
					</h4>
				@endif

				@if($call_to_action_present && empty($text) && !empty($call_to_action_label))
					<a class="call-to-action" href="{{ $call_to_action_url }}">
						{{ $call_to_action_label }}
					</a>
				@endif
			</div>

		</div>

		@if(empty($call_to_action_label))
		</a>
		@else

		<div class="single-back" style="background-color: {{ empty($bg_color) ? 'transparent' : $bg_color }}">
			<div class="content-writable">

				{!! $text !!}

			</div>
			@if($call_to_action_present)
				<a class="call-to-action" href="{{ $call_to_action_url }}">
					{{ $call_to_action_label }}
				</a>
			@endif
		</div>
		@endif

	@else

		@if($align === 'top')
			<div class="single__image-container"
				 style="background-size: {{ $group->image_mode }}; background-image: url({{ $image }});
				 height: {{ $group->image_height }}px">
			</div>
		@endif

		<div class="single__body {{ empty($bg_color) ? '' : 'single__body--need-inner-spacing' }}"
			 style="background-color: {{ empty($bg_color) ? 'transparent' : $bg_color }}">

			@if(!empty($title))
				<h3 class="single__title">
					{{ $title }}
				</h3>
			@endif

			@if(!empty($sub_title))
				<h4 class="single__subtitle">
					{{ $sub_title }}
				</h4>
			@endif

			@if(!empty($text))
				<div class="single__text content-writable">
					{!! $text !!}
				</div>
			@endif

			@if($call_to_action_present)
				<a class="see-more" href="{{ $call_to_action_url }}">
					{{ $call_to_action_label }}
				</a>
			@endif

		</div>

		@if($align === 'bottom')
			<div class="single__image-container"
				 style="background-size: {{ $group->image_mode }};  background-image: url({{ $image }});
				 height: {{ $group->image_height }}px">
			</div>
		@endif

	@endif

</div>

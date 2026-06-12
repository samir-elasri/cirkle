<div id="{{ \Carbon\Carbon::parse($start_datetime)->format('Y-m-d') }}" class="single">

	<div class="single__header">
		<div class="single__image-container" style="background-image: url({{ empty($image) ? setting()->events_default_image : $image }})"></div>
	</div>

	<div class="single__body single__body--need-inner-spacing">
		<h3 class="single__title">{{ $title }}</h3>
		{{-- <div class="single__date">{{ prettyDate($start_datetime) }}</div> --}}
		<div class="single__date">
			@if(!empty($end_datetime))
				@lang('main.events.from_date') {{ prettyDate($start_datetime) }} @lang('main.events.at_time') {{ date("H:i",strtotime($start_datetime)) }}, @lang('main.events.to_date') {{ prettyDate($end_datetime) }} @lang('main.events.at_time') {{ date("H:i",strtotime($end_datetime)) }}
			@else
				@lang('main.events.the_date') {{ prettyDate($start_datetime) }} @lang('main.events.at_time') {{ date("H:i",strtotime($start_datetime)) }}
			@endif
		</div>
		<div class="single__text">{{ trim_text($description, 200, ' [...]') }}</div>
		<div class="spacer"></div>
		<div class="see-more__content">
			<a class="see-more" href="{{ urlRouteName('basic-event', ['id' => $id, 'slug' => slug($title)]) }}" aria-label="{{ __('main.know-more') . ": " . $title }}">
				@lang('main.know-more')
			</a>
		</div>

	</div>
</div>

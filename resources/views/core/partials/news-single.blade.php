<div class="single">

	<div class="single__header">
		<div class="single__image-container" style="background-image: url({{ empty($singleNews->image) ? setting()->news_default_image : $singleNews->image }})"></div>
	</div>

	<div class="single__body single__body--need-inner-spacing">
		<h2 class="single__title">{{ $singleNews->title }}</h2>
		<div class="single__date">{{ prettyDate($singleNews->official_date) }}</div>
		<div class="single__description">{{ trim_text($singleNews->description, 200, '[...]') }}</div>

		<div class="spacer"></div>

		<div>
			<a class="see-more" href="{{ urlRouteName('news', ['id' => $singleNews->id, 'slug' => slug($singleNews->title)]) }}"  aria-label="{{ __('main.know-more') . ": " . $singleNews->title }}">
				@lang('main.know-more')
			</a>
		</div>

	</div>

</div>

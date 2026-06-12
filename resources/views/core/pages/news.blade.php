<section>
	<div class="news__header optimal-content-width">
		@if (!empty($image))
			<div class="news__image"><img src="{{ $image }}" alt="{{ $title }}"/></div>
		@endif
		<div class="news__title"><h4>{{ $title }}</h4></div>
		<div class="news__date">{{ prettyDate($publication_date) }}</div>
		<div class="news__categories">
			@foreach($categories as $categoryId => $categoryName)
				<span class="news__category">
						<a href="{{ urlRouteName('news-list', ['category' => $categoryId]) }}">
							{{ $categoryName }}
						</a>
					</span>
			@endforeach
		</div>
		<div class="news__description">{!! $description !!}</div>
	</div>
</section>

{!! $blocs !!}
@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])

<section>
	<div class="news__footer optimal-content-width">
		<a class="see-less" href="{{ urlRouteName('news-list') }}">@lang('main.back-all-news')</a>
	</div>
</section>

{!! $blocs !!}

@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])

<section>
	<div class="optimal-content-width">
		<p>@lang('main.page-not-found.description')</p>
		@include('core.partials.sitemap')
	</div>
</section>
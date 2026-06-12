
{!! $blocs !!}

@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])

<section>
	<div class="optimal-content-width">
		@include('core.partials.sitemap')
	</div>
</section>
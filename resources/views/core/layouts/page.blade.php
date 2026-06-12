<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include('core.partials.page.head')
<body
	class="{{ Session::has('show-inactive') ? 'show-inactive-blocs' : '' }} {{ Session::has('font-scaled') ? 'font-scaled' : ''  }}">

<!-- Barre d'administration -->
@if(isset($page_type))
	@include('core.partials.admin-bar')
@endif

<!-- Entête -->

@include('core.partials.page.header')

@if(!empty($slideshow_id))
	<!-- Diaporama -->
	@include('core.partials.slideshow', ['id' => $slideshow_id])
@elseif(!empty($banner_image))
	<!-- Bannière -->
	<div class="main__banner"
			style="background-image: url({{ $banner_image ?? '' }}); height: {{ round($banner_height / $optimal_content_width * 100) }}vw; max-height: {{ $banner_height }}px"></div>
@endif

<!-- Contenu principal -->
<main id="main" class="full-content-width">

	<!-- Espacement au-dessus de la page -->
	@include('core.partials.spacing', ['spacing' => $page_top_spacing])


	@if(!empty($title) || !empty($edit_url))
		<!-- Titre de la page -->
		<section class="main__title full-content-width">
			<div class="optimal-content-width">
				@if(!empty($title))
					<h1>{{ $title }}&nbsp;</h1>
				@endif
				@if($edit_url)
					<!-- Editer le bloc -->
					<a class="admin-bar__edit" href="{{ $edit_url }}" target="_blank"
					   title="@lang('admin.admin-bar.edit-page')"></a>
				@endif
			</div>
		</section>
	@endif

	<!-- Contenu principal -->
	<div class="main__layout">

		<div class="main__content {{ $has_right_column ? 'main__content--has-right-column' : '' }}">

			@include('partials.flashMessages')

			<!-- Contenu de la page -->
			@if(isset($view_name) && !empty($view_name))
				@include($view_name)
			@else
				{!! $content ?? '' !!}
			@endif

		</div>

		@if($has_right_column)
			<!-- Espacement entre le contenu principal et la colonne de droite -->
			<div style="min-width: var(--right-column-spacing)"></div>
			<!-- Colonne de droite -->
			<div class="main__right-column">
				@include('core.partials.spacing', ['spacing' => $first_bloc_top_spacing])
				@if(!empty($form_generator_id) && !empty($is_form_before_pubs))
					@include('core.partials.form', ['form_generator_id' => $form_generator_id])
				@endif
				@include('core.partials.pubs', compact('pubs'))
				@if(!empty($form_generator_id) && empty($is_form_before_pubs))
					@include('core.partials.form', ['form_generator_id' => $form_generator_id])
				@endif
			</div>
		@endif
	</div>

	<!-- Espacement au-dessus du pied de page -->
	@include('core.partials.spacing', ['spacing' => $footer_top_spacing])

</main>

<!-- Retour de haut de page -->
@include('core.partials.page.goto-top')

<!-- Pied de page -->
@include('core.partials.page.footer')

@include('core.partials.page.foot')

</body>
</html>

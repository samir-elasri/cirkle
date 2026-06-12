{{--

MODE

Si en mode pleine largeur :

	<section>
		<div class="bloc--full">...</div>
	</section>

Si en mode demi-largeur :

	- On ouvre la section que si l'on rend le bloc de gauche

		<section>
			<div class="bloc--left">...</div>

	- On ferme la section que si l'on rend le bloc de droite

			<div class="bloc--right">...</div>
		</section>

--}}

@if(!$half_width_mode || $half_width_mode == 'left')
	@include('core.partials.spacing', ['class' => $active ? '' : 'bloc--inactive', 'spacing' => $top_spacing])
    <section data-component="scrollfire" class="anim-fade-up blocs">
@elseif($half_width_mode == 'right')
	@include('core.partials.spacing', ['spacing' => $top_spacing])
@endif

<div id="bloc-{{ $id }}{{ empty($title) ? '' : '-' . slug($title) }}"
 class="bloc
		bloc-{{ $view_name }}																		{{-- Défini le nom de classe pour le bloc --}}
		bloc--{{ $half_width_mode ? $half_width_mode : 'full' }}									{{-- Détermine si le bloc est en mode demi-largeur --}}
		{{ $need_inner_spacing ? ' bloc--need-inner-spacing' : '' }}								{{-- Détermine si le bloc a besoin d'espacement à l'intérieur du bloc --}}
		{{ $bg_bleed ? 'bloc--bleed' : '' }}														{{-- Détermine si l'arrière-plan doit s'étendre --}}
		{{ $active ? '' : 'bloc--inactive' }}  														{{-- Détermine si le bloc est inactif --}}
		{{ isset($accordion) && $accordion ? 'bloc--accordion' : '' }}"
>

	<!-- Deboguage - Affichage du nom -->
	<div class="bloc__guide-label">
		Bloc {{ __('main.blocs.' . $view_name) }}
	</div>

	<div class="bloc__content {{ $half_width_mode ? 'half-content-width' : 'optimal-content-width' }} {{ $wait_ready ? 'wait-ready' : '' }}"
		 style="{{ $style ?? '' }}"
	>

		{{-- IMPORTANT! Ne jamais mettre aucun élément avant le titre et le contenu à cause la gestion de l'espacement du bloc --}}

		<!-- Titre -->
		@hasSection('bloc-content-title')
			@yield('bloc-content-title')
		@else
			@include('core.partials.bloc-title')
		@endif

		@hasSection('bloc-content-front')
		<!-- Contenu d'avant-plan -->
		<div class="bloc__content-front show-on-ready">
			@yield('bloc-content-front')
		</div>
		@endif

		<!-- Contenu d'arrière-plan -->
		<div class="bloc__content-back show-on-ready"
			 style="background-color: {{ empty($bg_color) ? 'transparent' : $bg_color }};"			{{-- Défini la couleur de fond du bloc --}}
		>
			@yield('bloc-content-back')
		</div>

		{{-- ----------------------------------------------------------------------------------------------------------------- --}}

		@if($wait_ready)
		<!-- Chargement -->
			@include('core.partials.loading', ['text' => __('main.loading.bloc', ['name' => __('main.blocs.' . $view_name)]), 'class' => 'bloc__loading show-on-wait-ready'])
		@endif

        @if($edit_url)
        <!-- Editer le bloc -->
        <a class="admin-bar__edit" href="{{ $edit_url }}" target="_blank" title="@lang('admin.admin-bar.edit-bloc') {{ __('main.blocs.' . $view_name) }}"></a>
        @endif

        @if(is_admin())
        <!-- Le bloc est inactif -->
        <span class="admin-bar__inactive-bloc" title="@lang('admin.admin-bar.inactive-bloc')"></span>
        @endif

	</div>

</div>

@if(!$half_width_mode || $half_width_mode == 'right')
</section>
@elseif(!isset($next_bloc) || !$next_bloc['half_width_mode'])
<div class="bloc bloc-text bloc--right"></div>
</section>
@endif

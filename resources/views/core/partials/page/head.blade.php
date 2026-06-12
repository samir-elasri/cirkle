<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	@if(!empty($metadata))
		<title>{{ $metadata->title }}</title>
		<meta name="description" content="{{ strip_tags($metadata->description) }}">

		<!-- Twitter Card data -->
		<meta name="twitter:card" content="summary">
		<meta name="twitter:title" content="{{ $metadata->tw_title }}">
		<meta name="twitter:description" content="{{ $metadata->tw_description }}">
		@if(!empty($metadata->tw_image))
			<meta name="twitter:image" content="{{ $metadata->tw_image }}">
		@endif

		<!-- Open Graph data -->
		<meta property="og:type" content="website">
		<meta property="og:locale" content="{{ app()->getLocale() }}_CA">
		<meta property="og:site_name" content="{{ setting('company_name') }}">
		<meta property="og:url" content="{{ Request::fullUrl() }}">
		<meta property="og:title" content="{{ $metadata->fb_title }}"/>
		<meta property="og:description" content="{{ $metadata->fb_description }}"/>
		@if(!empty($metadata->fb_image))
			<meta property="og:image" content="{{ $metadata->fb_image }}"/>
			<meta property="og:image:secure" content="{{ $metadata->fb_image }}"/>
		@endif
	@else
		<title>{{ setting('company_name') }}</title>
	@endif

	{{-- Bonne pratique pour SEO: La page '/' du site a le même contenu que la page '/fr', Google n'aime pas --}}
	@if (!empty($custom_code) && $custom_code == 'home')
		<link rel="canonical" href="{{ url('') }}/{{ config('app.fallback_locale') }}">
	@endif
	<meta name="robots" content="index,follow">
	<meta name="GOOGLEBOT" content="index,follow">
	<meta name="revisit-after" content="15 days">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="mbiance">
	<meta name="csrf-token" content="{{ csrf_token() }}"/>

	@if(config('google.recaptcha.active'))
		<meta property="recaptcha:site_key" content="{{ config('google.recaptcha.site_key') }}">
		<meta property="recaptcha:input_name" content="{{ config('google.recaptcha.input_name') }}">
	@endif

	<!-- Icône -->
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

	<!-- Web manifest -->
	@php ($locale = app()->getLocale())
	<link rel="manifest" href="{{ config('app.multilingual') ? "/index.{$locale}.webmanifest" : "/index.webmanifest" }}">

	<!-- Variables pour les feuilles de styles -->
	@include('core.partials.page.style')

	<!-- Feuilles de styles principale -->
	{{-- For Chrome --}}
	<link rel="preload" href="{{ asset_with_version('/dist/compiled/semantic/semantic.min.css') }}" as="style">
	<link rel="preload" href="{{ asset_with_version('/dist/compiled/main.min.css') }}" as="style">

	{{-- For the rest --}}
	<link rel="stylesheet" href="{{ asset_with_version('/dist/compiled/semantic/semantic.min.css') }}">
	<link rel="stylesheet" href="{{ asset_with_version('/dist/compiled/main.min.css') }}">

	@if(config('google.analytics.measure_id'))
		<!-- Global site tag (gtag.js) - Google Analytics (GA4) -->
		<script async
				src="https://www.googletagmanager.com/gtag/js?id={{ config('google.analytics.measure_id') }}"></script>
		<script>
			window.dataLayer = window.dataLayer || [];

			function gtag() {
				dataLayer.push(arguments);
			}

			gtag('js', new Date());
			gtag('config', '{{ config('google.analytics.measure_id') }}');
		</script>
	@endif

	<!-- Données structurées -->
	@if (!empty($structured_datas))
		@foreach($structured_datas as $structured_data)
			<script type="application/ld+json">
				@json($structured_data)
			</script>
		@endforeach
	@endif

{{-- <script>
		// Check that service workers are supported
		if ('serviceWorker' in navigator) {
			// Use the window load event to keep the page load performant
			window.addEventListener('load', () => {
				navigator.serviceWorker.register('{{asset_with_version('/sw.js')}}');
			});
		}
	</script> --}}

	<script type="module" src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.1/cdn/shoelace-autoloader.js"></script>
	<script type="module">
	import { registerIconLibrary } from 'https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.1/cdn/utilities/icon-library.js';
	registerIconLibrary('system', { resolver: name => `/dist/assets/icons/${name}.svg`, mutator: svg => svg.setAttribute('fill', 'currentColor') });
	</script>

</head>

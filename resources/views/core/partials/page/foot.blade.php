<!-- Browser Sync -->
@if(config('app.browser-sync', false))
	<script id="__bs_script__">//<![CDATA[
		document.write("<script async src='//HOST:3000/browser-sync/browser-sync-client.js?v=2.26.7'><\/script>".replace("HOST", location.hostname));//]]>
	</script>
@endif

@if(config('cart.cart_type') === 'basic')
	<!-- Stripe -->
	<script type="text/javascript" defer src="https://js.stripe.com/v3/"></script>
@endif

@if(config('google.maps.active'))
	<!-- Google Maps -->
	<script defer type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('google.api.key') }}&libraries=places"></script>
@endif

@if(config('google.recaptcha.active'))
	<!-- Google reCaptcha -->
	<script type="text/javascript"
			src="//www.google.com/recaptcha/api.js?render={{ config('google.recaptcha.site_key') }}"></script>
@endif

<!-- Script principal -->
@if(config('app.useProductionJS', true))
	<script type="text/javascript" defer src="{{ asset_with_version('/dist/compiled/main.prod.js') }}"></script>
@else
	<script type="text/javascript" defer src="{{ asset_with_version('/dist/compiled/main.js') }}"></script>
@endif


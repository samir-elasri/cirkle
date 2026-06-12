<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ setting('company_name') }}</title>

	<!-- Icône -->
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<!-- Feuille de styles principale -->
	<link rel="stylesheet" href="{{ asset_with_version('/dist/compiled/main.min.css') }}">

</head>
<body class="maintenance">
	<div class="maintenance__content">
		<img src="{{ setting()->main_logo_image }}" alt="{{ setting('company_name') }}">
		<h1>@lang('main.maintenance')</h1>
		{!! setting()->maintenance_text !!}
		<a href="https://mbiance.com" target="_blank"><img class="maintenance__corpo" src="/dist/img/logo-mbiance-blue.png" alt=""></a>
	</div>
</body>
</html>

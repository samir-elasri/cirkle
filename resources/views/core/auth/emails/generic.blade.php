<!DOCTYPE html>
<html lang="{{ App::getLocale() == 'fr' ? 'fr-CA' : 'en-EN' }}" style="font-family: sans-serif;">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
			{!!  $data['text'] !!}
		</div>
	</body>
</html>

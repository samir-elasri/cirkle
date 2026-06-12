<?php

return [

	'api' => [
		'key' => env('GOOGLE_API_KEY'),
	],

	'analytics' => [
		'measure_id' => env('GOOGLE_ANALYTICS_MEASURE_ID'),
	],

	'maps' => [
		'active' => env('GOOGLE_MAPS_ACTIVE', false),
	],

	'recaptcha' => [
		'active' =>  env('GOOGLE_RECAPTCHA_ACTIVE', true),
		'site_key' => env('GOOGLE_RECAPTCHA_SITE_KEY'),
		'secret_key' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
		'input_name' => env('GOOGLE_RECAPTCHA_INPUT_NAME', 'g-recaptcha-response'),
	],
];

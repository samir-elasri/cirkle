<?php

return [

	'headers' => [
		'Content-Security-Policy'     => env('HEADER_CONTENT_SECURITY_POLICY'),
		'Strict-Transport-Security'   => env('HEADER_STRICT_TRANSPORT_SECURITY'),
		'X-Content-Type-Options'      => env('HEADER_X_CONTENT_TYPE_OPTIONS'),
		'X-Frame-Options'             => env('HEADER_X_FRAME_OPTIONS'),
//		'Access-Control-Allow-Origin' => env('HEADER_ACCESS_CONTROL_ALLOW_ORIGIN'),
		'Expect-CT'                   => env('HEADER_EXPECT_CT'),
		'Permissions-Policy'          => env('HEADER_PERMISSIONS_POLICY'),
		'Referrer-Policy'             => env('HEADER_REFERRER_POLICY'),
	],

	'csp-sites' => [
		'*.vimeo.com',
		'*.youtube.com',
		'*.stripe.com',
		'*.google.com',
		'*.google.ca',
		'*.googletagmanager.com',
		'*.google-analytics.com',
		'*.moatads.com',
		'*.addthisedge.com',
		'*.doubleclick.net',
		'*.plyr.io',
		'*.ytimg.com',
		'noembed.com',
		'cdn-cookieyes.com',
		'*.addtoany.com',
		'*.facebook.com',
		'facebook.com',
		'*.googleapis.com',
		'*.gstatic.com',
		'cdn.jsdelivr.net',
		'*.marker.io',
		's3.eu-west-1.amazonaws.com', // needed for marker.io
	]
];

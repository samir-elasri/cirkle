<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Application Payment Method
    |--------------------------------------------------------------------------
    |
    | Determine if the application accepts those payment methode as a valid way to pay,
    | To shut off, remove them from the list in the .env file.
    | .ENV SYNTAX : paypal,stripe,other
    | AVAILABLE OPTION : paypal
    |
    */

	'payment_method' => [
		'paypal' => in_array('paypal', explode(',', env('PAYMENT_METHOD', '')), true),
	],

	/*
    |--------------------------------------------------------------------------
    | Application Cart Model
    |--------------------------------------------------------------------------
    |
    | Determine if the application use a basic cart system or a more advanced one
    | To change, change the value in the .env file.
    | AVAILABLE OPTION : basic
    |
    */

	'cart_type' => 'basic',

];

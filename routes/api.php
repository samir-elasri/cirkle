<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
if (config('api.active')) {
	Route::prefix('/api')->name('api.')->middleware('localization:api')->group(static function () {

		Route::prefix('auth')->name('auth.')->group(static function () {
			Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
			Route::post('register', ['as' => 'register', 'uses' => 'AuthController@register']);
			Route::post('lost-password', ['as' => 'lost-password', 'uses' => 'AuthController@lostPassword']);
		});
	});
}

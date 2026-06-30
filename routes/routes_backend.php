<?php

use App\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

function enumFiles($dir, $ext, $subdir)
{
	$list = [];
	$scan = scandir($dir, 0);
	foreach ($scan as $i => $filename) {
		if ($filename != '.' && $filename != '..') {
			$path = $dir . '/' . $filename;
			if (is_dir($path)) {
				if ($subdir) {
					$list = array_merge($list, enumFiles($path, $ext, $subdir));
				}
			} else {
				if (preg_match('/\.(' . $ext . ')$/i', $path)) {
					$list[] = $path;
				}
			}
		}
	}
	return $list;
}

View::addLocation(app_path('Mbiance/Views'));

Route::namespace('Admin')->prefix('/admin')->name('admin.')->group(function () {
	Route::get('/', 'AdminAuthController@getLogin');
	Route::get('login', 'AdminAuthController@getLogin')->name('login');

	// Removed recaptcha from admin login becasue no fallback exists if user is tagged as both
	// Todo implement 2 step
	Route::post('login', 'AdminAuthController@postLogin')->name('login.post');

	// Route::group(array('prefix' => 'admin', 'before' => 'auth|api.csrf'), function () {
	Route::middleware('auth:users')->group(function () {

		//ROUTE CUSTOM
		Route::get('/', 'AdminAuthController@getDashboard')->name('home');
		Route::get('dashboard', 'AdminAuthController@getDashboard')->name('dashboard');
		Route::get('clearCache', 'AdminAuthController@clearCache')->name('clearCache');
		Route::get('logout', 'AdminAuthController@getLogout')->name('logout');
		Route::get('download', 'AdminAuthController@download')->name('download');
		Route::get('queryData', 'GridController@queryData')->name('grid');
		Route::post('setGridState', 'AdminGenericController@setGridState')->name('setGridState');
		Route::post('resetGridState', 'AdminGenericController@resetGridState')->name('resetGridState');
		Route::get('models-test', 'TestsController@modelsTests')->name('modelsTest');
        Route::post('formTest', 'TestsController@formTest')->name('formTest');

        Route::get('fiche', 'AdminFicheController@create')->name('fiche.create');
        Route::post('fiche', 'AdminFicheController@store')->name('fiche.store');

        // Activer/désactiver les plateformes françaises (« À VENIR ») — Denis 30.06
        Route::get('plateformes', 'AdminPlatformController@edit')->name('plateformes.edit');
        Route::post('plateformes', 'AdminPlatformController@update')->name('plateformes.update');

        Route::post('excel/import', 'AdminExcelController@import')->name('excel.import');

		/**
		 * ROUTES POUR LE SERVICE TIERS ELFINDER
		 */
		Route::post('smtp', 'AdminAuthController@setSmtp')->name('smtp');
		Route::get('smtp', 'AdminAuthController@getSmtp')->name('smtp');
		Route::get('translations', 'AdminAuthController@gettranslations')->name('translations');
		Route::view('translations/{group}/edit', 'generic.translations_edit')->name('translations.edit');
		Route::post('translations/{group}', 'AdminAuthController@translationUpdate')
			->name('translations.update')
			->withoutMiddleware([TrimStrings::class]);
		Route::get('filescontainer', 'AdminAuthController@getFilesContainer')->name('elfinder.iframe');

		Route::post('/examples/upload', 'AdminGenericController@upload');

		Route::get('/impersonate/{guard}/{id}', function (Illuminate\Http\Request $request, $guard, $id) {
			if (Auth::guard('users')->user()->is_admin && $guard !== 'users') {
				Auth::guard($guard)->loginUsingId($id);
			}

			return redirect()->back();
		});

		Route::get('/changeDebug/{debug}', 'AdminAuthController@changeDebug');

		/**
		 * GÉNÉRATION AUTOMATIQUE DES ROUTES
		 * À PARTIR DES INFORMATIONS CONTENUS DANS LA CONFIGURATION GÉNÉRALE
		 * propriété 'routes-backend' dans /application/app/config/app.php
		 */
		RoutingUtility::setEntityRoutesFromConfig();
	});
});

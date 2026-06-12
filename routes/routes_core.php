<?php

use App\Models\Core\Page;

$locale = config('app.fallback_locale');
$localeRegex = implode('|', getLocales());

if (isMultilingual()) {

	// Redirige vers une langue.
	// Spec Cirkle (PAGE ACCUEIL 010626) : la page d'accueil / frontispice ouvre en ANGLAIS;
	// le visiteur choisit ensuite sa plateforme (sélecteur 4 plateformes).
	Route::get('/', function () {
		return Redirect::to('en');
	});

	// $route_params = ['prefix' => '{locale?}', 'before' => 'localization'];
	if (!RoutingUtility::isAdmin()) {
		$requestLocale = Request::segment(1);

		if ($requestLocale === 'api') {
			$requestLocale = Request::get('lang');
		}

		$locale = in_array($requestLocale, getLocales())
			? $requestLocale
			: config('app.fallback_locale', 'fr');

		if (!in_array($locale, getLocales())) {
			$locale = 'fr';
		}
	}
}

Route::get('/sitemap.xml', 'PageController@sitemap')->name('robots');

Route::middleware('cache.headers:public;max_age=2628000;etag')->group(static function () {
	// Custom image cache
	Route::get('imgcache/{source?}', [
		'as'   => 'imgcache',
		'uses' => 'ImageController@show'
	])->where('source', '(.*)');
});


Route::middleware('localization')
	->prefix(isMultilingual() ? '{locale?}' : '')
	->where(['locale' => "($localeRegex)"])
	->group(function () use ($locale) {

		createRoutes($locale);

		Route::post('handle-form/{id}', 'PageController@handleGeneratedForm')
			->where(['id' => '[0-9]+', 'slug' => '.*'])
			->middleware('recaptcha')
			->name('handleForm.post');
		Route::get('font-scaled', 'PageController@fontScaled')
			->name('fontScaled');
		Route::get('show-inactive', 'PageController@showInactiveBlocs')
			->name('showInactive');

		// Routes pour les pages standards
		Route::get('{id}/{slug?}', 'PageController@standard')
			->where(['id' => '[0-9]+', 'slug' => '.*'])
			->name('standard');

		// Routes pour les pages personnalisées
		if (in_array($locale, getLocales(), true)) {
			foreach (Page::getRoutes($locale) as $path => $id) {
				Route::get($path, 'PageController@custom');
			}
		}

		// Routes pour les pages intégrées
		foreach (config('routes.front-end') as $name => $params) {
			$uri = Arr::get(Arr::get($params, 'uri', []), $locale, false);
			if ($uri) {
				$methods = Arr::get($params, 'methods', 'get');
				$middleware = Arr::get($params, 'before', '');
				$r = Route::match($methods, $uri, 'PageController@integrated')
					->where(Arr::get($params, 'where', []))
					->name($name);
				if ($middleware) {
					$r->middleware($middleware);
				}
			}
		}
	});

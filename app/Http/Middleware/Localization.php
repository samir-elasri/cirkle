<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;
use Route;

class Localization
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  Request  $request
	 * @param  Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $type = 'web')
	{
		if ($type === 'web') {
			$locale = in_array(Route::input('locale'), getLocales())
				? Route::input('locale')
				: config('app.fallback_locale', 'fr');

			$params = config('app.setLocale.' . $locale);

			App::setLocale($locale);
			setlocale(LC_ALL, $params[0], $params[1]);
			setlocale(LC_NUMERIC, 'english', 'en_CA');

			// Remove locale from request
			$request->route()->forgetParameter('locale');

		} elseif($type === 'api') {
			if ($request->has('lang') && array_key_exists($request->get('lang'), config('app.setLocale'))) {
				app()->setLocale($request->get('lang'));
			}
		}

		return $next($request);
	}
}

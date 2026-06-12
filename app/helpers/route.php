<?php

use App\Models\Core\Page;

function urlRouteName($routeName, $params = [], $isAbsolute = false): string
{
	if (isMultilingual()) {
		$params['locale'] = app()->getLocale();
	} else {
		unset($params['locale']);
	}

	$anchor = Arr::pull($params, '#');

	return route($routeName, $params, $isAbsolute) . ($anchor ? '#' : '') . $anchor;
}

function adminRouteName($routeName, $params = [], $isAbsolute = false): string
{
	$anchor = Arr::pull($params, '#');

	return route($routeName, $params, $isAbsolute) . ($anchor ? '#' : '') . $anchor;
}

/**
 * Not retrocompatible with old urlRouteName because code is much slower and should only be used with intent
 *
 * @param $routeName
 * @param  array  $params
 * @param  string  $locale
 * @return mixed|string
 */
function localizedUrl($routeName, $params = [], $locale = 'fr') {
	$page = Page::whereIntegrated(true)
		->whereLabel($routeName)
		->first();

	$config = config('routes.front-end');
	$route = Arr::get($config, 'news', []);
	$uri = Arr::get($route, "uri.{$locale}", []);
	$attributes = $page ? [] : Arr::get($route, 'page', []);

	if (!$page) {
		// Détermine et remplace les paramètres de la route
		if (preg_match_all('/{(\w+)\??}/', $uri, $matches, PREG_SET_ORDER)) {
			foreach ($matches as [$pattern, $name]) {
				$uri = str_replace($pattern, Arr::get($params, $name, Route::input($name)), $uri);
			}
		}

		$attributes[$locale]['custom_url'] = $uri;

		$tempPage = new Page($attributes);
		$tempPage->label = 'news';
	}

	return $page->getUrl($locale);
}

function standardRoute($params, $locale): string
{
	if (isMultilingual()) {
		$params['locale'] = $locale;
	} else {
		unset($params['locale']);
	}

	return route('standard', $params, false);
}

function urlPath($path, $locale = null): string
{
	if (isMultilingual()) {
		if (!isset($locale)) {
			$l = app()->getLocale();
			$locale = (!empty($l)) ? $l : config('app.fallback_locale');
		}

		$path = '/' . $locale . $path;
	}

	return strlen($path) > 1 ? rtrim($path, '/') : $path;
}

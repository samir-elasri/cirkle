<?php


namespace App\Mbiance\MediaUtility;


use Arr;
use Illuminate\Support\HtmlString;

class HtmlBuilder extends \Collective\Html\HtmlBuilder
{
	/**
	 * Generate an HTML image element.
	 *
	 * @param  string  $url
	 * @param  string|null  $alt
	 * @param  array  $attributes
	 * @param  bool|null  $secure
	 *
	 * @return HtmlString
	 */
	public function image($url, $alt = null, $attributes = [], $secure = null): HtmlString
	{
		if(empty($url)) {
			return new HtmlString('');
		}

		if($lazy = Arr::get($attributes, 'data-lazy') ?? false) {
			$attributes['data-lazy'] = imageCache($lazy, $attributes, true);
		} else {
			$attributes['loading'] = 'lazy';
		}

		if (!str_ends_with($url, '.svg') && !str_ends_with($url, '.gif')) {
			$url = imageCache($url, $attributes, true);
		}

		// Remove width and height as to not prevent responsive images.
		Arr::forget($attributes, 'width');
		Arr::forget($attributes, 'height');

		return parent::image($url, $alt, $attributes, $secure);
	}

	/**
	 * Generate a link to a JavaScript file.
	 *
	 * @param  string  $url
	 * @param  array  $attributes
	 * @param  bool  $secure
	 *
	 * @return HtmlString
	 */
	public function script($url, $attributes = [], $secure = null)
	{
		$url = asset_with_version($url);

		return parent::script($url, $attributes, $secure);
	}

	/**
	 * Generate a link to a CSS file.
	 *
	 * @param string $url
	 * @param array  $attributes
	 * @param bool   $secure
	 *
	 * @return HtmlString
	 */
	public function style($url, $attributes = [], $secure = null)
	{
		$url = asset_with_version($url);

		return parent::style($url, $attributes, $secure);
	}
}

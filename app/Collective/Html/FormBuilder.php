<?php

namespace App\Collective\Html;

use Illuminate\Support\Arr;
use RuntimeException;
use Illuminate\Support\HtmlString;
use Collective\Html\FormBuilder as BaseBuilder;

class FormBuilder extends BaseBuilder
{
	protected $reserved = ['method', 'url', 'route', 'action', 'files', 'recaptcha'];

	protected $needReCaptcha;

	public function open(array $options = []): HtmlString
	{
		$method = Arr::get($options, 'method', 'post');

		$attributes['method'] = $this->getMethod($method);
		$attributes['action'] = $this->getAction($options);

		$this->needReCaptcha = $this->checkIfNeedReCaptcha($method, $options);

		// Si un url ou une action est définit
		if ($this->needReCaptcha && ($options['url'] ?? $options['action'] ?? false)) {
			// Détermine si l'url doit avoir le middleware 'recaptcha'
			$this->needReCaptcha = $this->checkIfUrlMustHaveReCaptchaMiddleware($attributes['action'], $method);
		}

		if($this->needReCaptcha) {
			$attributes['data-component'] = 'recaptcha';
		}

		$attributes['accept-charset'] = 'UTF-8';

		$append = $this->getAppendage($method);

		if ($this->needReCaptcha) {
			$append .= $this->recaptcha();
		}

		if (isset($options['files']) && $options['files']) {
			$options['enctype'] = 'multipart/form-data';
		}

		$attributes = array_merge(
			$attributes, Arr::except($options, $this->reserved)
		);

		$attributes = $this->html->attributes($attributes);

		return $this->toHtmlString('<form' . $attributes . '>' . $append);
	}

	public function recaptcha($options = []): HtmlString
	{
		return config('google.recaptcha.active') ? $this->captcha($options) : new HtmlString('');
	}

	public function captcha($options = []): HtmlString
	{
		return $this->hidden(config('google.recaptcha.input_name', 'g-recaptcha-response'), null, $options);
	}

	protected function getRouteAction($options): string
	{
		$parameters = [];

		if (is_array($options)) {
			$name = $options[0];
			$parameters = array_slice($options, 1);

			if (array_keys($options) === [0, 1]) {
				$parameters = head($parameters);
			}
		} else {
			$name = $options;
		}

		if ($this->needReCaptcha) {
			// Détermine si la route doit avoir le middleware 'recaptcha'
			$this->checkIfRouteMustHaveReCaptchaMiddleware(
				app('router')->getRoutes()->getByName($name)
			);
		}

		return $this->url->route($name, $parameters);
	}

	protected function checkIfNeedReCaptcha(string $method, array $options): bool
	{
		return strtoupper($method) !== 'GET' && Arr::get($options, 'recaptcha', true);
	}

	protected function checkIfUrlMustHaveReCaptchaMiddleware($action, $method): bool
	{
		if (in_admin() && is_admin()) {

			return false;

		}

		$requested = app('request')->create($action, $method);

		/** @noinspection NullPointerExceptionInspection */
		if ($requested->getHost() === request()->getHost()) {
			$this->checkIfRouteMustHaveReCaptchaMiddleware(
				app('router')->getRoutes()->match($requested)
			);
		} else {
			return false;
		}

		return true;
	}

	protected function checkIfRouteMustHaveReCaptchaMiddleware($route): void
	{
		if ($route && !in_array('recaptcha', $route->gatherMiddleware(), true)) {
			/** @noinspection AllyPlainPhpInspection */
			throw new RuntimeException("The 'recaptcha' middleware is missing for the form's route [{$route->getName()}].");
		}
	}

	public function reCaptchaIsActive(): bool
	{
		return config('google.recaptcha.active') && !(in_admin() && is_admin());
	}
}

<?php

namespace App\Providers;

use ReCaptcha\ReCaptcha;
use App\Collective\Html\FormBuilder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class ReCaptchaServiceProvider extends ServiceProvider implements DeferrableProvider
{
	public function boot(): void
	{
		if ($this->reCaptchaIsActive()) {
			$this->app['validator']->extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
				$app = app();
				$request = $app['request'];

				/** @var ReCaptcha $recaptcha */
				$recaptcha = $app['recaptcha']->setExpectedHostname($request->getHttpHost());

				if ($parameters[0] ?? '') {
					$recaptcha->setExpectedAction($parameters[0]);
				}

				if ($parameters[1] ?? '') {
					$recaptcha->setScoreThreshold($parameters[1]);
				}

				return $recaptcha->verify($value, $request->getClientIp())->isSuccess();
			});
		}
	}

	public function register(): void
	{
		$this->registerReCaptcha();
		$this->registerFormBuilder();
	}

	protected function registerReCaptcha(): void
	{
		$this->app->singleton('recaptcha', function ($app) {
			return new ReCaptcha($app['config']['google.recaptcha.secret_key']);
		});
	}

	protected function registerFormBuilder(): void
	{
		$this->app->singleton('form', function ($app) {
			$form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token(), $app['request']);

			return $form->setSessionStore($app['session.store']);
		});
	}

	public function provides(): array
	{
		return $this->reCaptchaIsActive()
			? ['recaptcha', 'form']
			: ['form'];
	}

	public function reCaptchaIsActive(): bool
	{
		return $this->app['config']['google.recaptcha.active'] && !(in_admin() && is_admin());
	}
}

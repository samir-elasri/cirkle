<?php


namespace App\Mbiance\MediaUtility;


use Collective\Html\FormBuilder;

class HtmlServiceProvider extends \Collective\Html\HtmlServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerHtmlBuilder();

		$this->registerFormBuilder();

		$this->app->alias('html', HtmlBuilder::class);
		$this->app->alias('form', FormBuilder::class);

		$this->registerBladeDirectives();
	}

	/**
	 * Register the HTML builder instance.
	 *
	 * @return void
	 */
	protected function registerHtmlBuilder()
	{
		$this->app->singleton('html', function ($app) {
			return new HtmlBuilder($app['url'], $app['view']);
		});
	}
}
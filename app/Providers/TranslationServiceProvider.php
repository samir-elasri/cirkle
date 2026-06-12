<?php

namespace App\Providers;

use App\Translation\FileLoader;
use App\Translation\Translator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Cette classe est identique à sa référence [Illuminate\Translation\TranslationServiceProvider].
 * Seules les références aux classes [FileLoader] et [Translator] ont été remplacées par [App\Translation\FileLoader]
 * et [App\Translation\Translator] respectivement.
 *
 * @reference Illuminate\Translation\TranslationServiceProvider
 * @override FileLoader
 * @override Translator
 */
class TranslationServiceProvider extends ServiceProvider implements DeferrableProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerLoader();

		$this->app->singleton('translator', function ($app) {
			$loader = $app['translation.loader'];

			// When registering the translator component, we'll need to set the default
			// locale as well as the fallback locale. So, we'll grab the application
			// configuration so we can easily get both of these values from there.
			$locale = $app['config']['app.locale'];

			$trans = new Translator($loader, $locale);

			$trans->setFallback($app['config']['app.fallback_locale']);

			return $trans;
		});
	}

	/**
	 * Register the translation line loader.
	 *
	 * @return void
	 */
	protected function registerLoader()
	{
		$this->app->singleton('translation.loader', function ($app) {
			return new FileLoader(
				$app['files'],
				$app['path.lang'],
				$app['path.lang.compiled'],
				$app['path.lang.overrides']
			);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['translator', 'translation.loader'];
	}
}

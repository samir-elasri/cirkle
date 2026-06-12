<?php

namespace Mbiance\AdminUtility;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

	public function register()
	{

		$this->app->singleton('formutility', function ($app) { //bindShared pour Singleton
			return new FormUtility($app['form'], $app['modelutility'], $app['pageutility']); //injection de dépendance
		});

		$this->app->singleton('gridutility', function ($app) {
			return new GridUtility($app['pageutility'], $app['modelutility']);
		});

		$this->app->singleton('modelutility', function () {
			return new ModelUtility();
		});

		$this->app->singleton('pageutility', function ($app) {
			return new PageUtility($app['modelutility']);
		});

		$this->app->singleton('routingutility', function ($app) {
			return new RoutingUtility($app['modelutility']);
		});

		$this->app->singleton('stringutility', function () {
			return new StringUtility();
		});
	}

}

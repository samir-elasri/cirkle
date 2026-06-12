<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Route;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		//

		parent::boot();
	}

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		// $this->mapApiRoutes();

		// $this->mapWebRoutes();

		$this->mapRoutes();

		$this->mapPluginRoutes();

		//
	}

	protected function mapRoutes()
	{
		Route::middleware('routes')
			->namespace('App\Http\Controllers')
			->group(base_path('routes/routes.php'));
	}

	protected function mapPluginRoutes()
	{
		Route::middleware('routes')
			->group(base_path('routes/plugins.php'));
	}

	/**
	 * Define the "web" routes for the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	// protected function mapWebRoutes()
	// {
	// 	Route::middleware('web')
	// 		->namespace($this->namespace)
	// 		->group(base_path('routes/web.php'));
	// }

	/**
	 * Define the "api" routes for the application.
	 *
	 * These routes are typically stateless.
	 *
	 * @return void
	 */
	protected function mapApiRoutes()
	{
		Route::middleware('api')
			->namespace('App\Http\Controllers\Api')
			->group(base_path('routes/api.php'));
	}
}

<?php

namespace App\Providers;

use App\Console\Commands\MigrationCreator;
use App\Mixins\PrettyCarbon;
use App\Models\Core\Setting;
use Carbon\Carbon;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use ReflectionException;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 * @throws ReflectionException
	 */
	public function register()
	{
		$this->app->singleton('setting', static function () {
			return Setting::getInstance();
		});

		Carbon::mixin(new PrettyCarbon);

		$this->registerCreator();
		$this->registerMigrateMakeCommand();
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Paginator::useBootstrap();
		Schema::defaultStringLength(191);
	}

	/**
	 * Register the migration creator.
	 *
	 * @return void
	 */
	protected function registerCreator()
	{
		$this->app->when(\Illuminate\Database\Migrations\MigrationCreator::class)
			->needs('$customStubPath')
			->give(function ($app) {
				return $app->basePath('stubs');
			});

		$this->app->extend('migration.creator', function ($command, $app) {
			return new MigrationCreator($app['files'], $app->basePath('stubs'));
		});
	}

	/**
	 * Register the command.
	 *
	 * @return void
	 */
	protected function registerMigrateMakeCommand()
	{
		$this->app->extend(MigrateMakeCommand::class, function ($command, $app) {
			// Once we have the migration creator registered, we will create the command
			// and inject the creator. The creator is responsible for the actual file
			// creation of the migrations, and may be extended by these developers.
			$creator = $app['migration.creator'];

			$composer = $app['composer'];

			return new MigrateMakeCommand($creator, $composer);
		});
	}
}

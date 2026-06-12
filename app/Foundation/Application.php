<?php

namespace App\Foundation;

use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
	protected function bindPathsInContainer()
	{
		parent::bindPathsInContainer();

		$this->instance('path.lang.compiled', $this->langCompiledPath());
		$this->instance('path.lang.overrides', $this->langOverridesPath());
	}

	public function publicPath($path = ''): string
	{
		return $this->basePath('public_html'.($path ? DIRECTORY_SEPARATOR.$path : $path));
	}

	public function langCompiledPath(string $path = null): string
	{
		return $this->publicPath('dist'.DIRECTORY_SEPARATOR.'compiled'.DIRECTORY_SEPARATOR.'lang'.($path ? DIRECTORY_SEPARATOR.$path : $path));
	}

	public function apiCompiledPath(string $path = null): string
	{
		return $this->basePath('api'.($path ? DIRECTORY_SEPARATOR.$path : $path));
	}

	public function langOverridesPath(string $path = null): string
	{
		return $this->publicPath('lang'.($path ? DIRECTORY_SEPARATOR.$path : $path));
	}

	/**
	 * Determine if the application is in the production environment.
	 *
	 * @return bool
	 */
	public function isProduction()
	{
		return in_array($this['env'], ['prod', 'production']);
	}
}

<?php

namespace App\Translation;

use Artisan;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader as BaseLoader;

class FileLoader extends BaseLoader
{
	protected $path;
	protected $compiledPath;
	protected $overridesPath;

	/**
	 * Create a new file loader instance.
	 *
	 * @param Filesystem $files
	 * @param string $path
	 * @param string $compiledPath
	 * @param string $overridesPath
	 */
	public function __construct(Filesystem $files, string $path, string $compiledPath, string $overridesPath)
	{
		parent::__construct($files, $path);

		$this->path = $path;
		$this->compiledPath = $compiledPath;
		$this->overridesPath = $overridesPath;
	}

	/**
	 * Load the messages for the given locale.
	 *
	 * @param string $locale
	 * @param string $group
	 * @param string|null $namespace
	 * @return array
	 */
	public function load($locale, $group, $namespace = null): array
	{
		if (app()->isProduction()) {
			// Tente de compiler le fichier des traductions pour la langue spécifiée s'il n'existe pas déjà.
			if (!$this->files->exists($filename = "$this->compiledPath/$locale.json")) {
				@Artisan::call('locales:compile', ['locales' => $locale]);
			}
			return $this->loadJson($filename);
		}

		// Conserve le comportement par défaut pour les fichiers JSON.
		if ($group === '*' && $namespace === '*') {
			return $this->loadJsonPaths($locale);
		}

		if (is_null($namespace) || $namespace === '*') {
			// Supplante les traductions originales par celles créés à partir du gestionnaire.
			return array_replace_recursive(
				$this->loadJson("$this->path/$group.$locale.json"),
				$this->loadJson("$this->overridesPath/$group.$locale.json")
			);
		}

		// Conserve le comportement par défaut pour les traductions avec namespace.
		return $this->loadNamespaced($locale, $group, $namespace);
	}

	/**
	 * Charge un fichier JSON.
	 *
	 * @param string $filename
	 * @return array
	 * @noinspection JsonEncodingApiUsageInspection
	 */
	protected function loadJson(string $filename): array
	{
		return @json_decode(@file_get_contents($filename), true)
			?? [];
	}
}

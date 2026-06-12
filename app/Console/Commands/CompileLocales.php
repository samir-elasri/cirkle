<?php

namespace App\Console\Commands;

use Arr;
use Illuminate\Console\Command;
use App\Foundation\Application;
use Symfony\Component\Finder\Finder;

class CompileLocales extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'locales:compile {locales?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Compile all locales.';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 * @noinspection JsonEncodingApiUsageInspection
	 */
	public function handle(): void
	{
		/** @var $app Application */
		$app = app();
		$locales = getLocales();

		if($only = $this->argument('locales')) {
			$only = explode(',', $only);
			$locales = array_intersect($only, $locales);
		}

		// Compile toutes les langues de l'application.
		foreach($locales as $locale) {

			// Charge toutes les traductions de la langue courante.
			$stack = [];
			$extension = "$locale.json";
			foreach(Finder::create()->in(lang_path())->files()->name("*.$extension") as $file) {
				// Récupère le nom du groupe à partir du nom de fichier moins l'extension.
				$group = $file->getBasename(".$extension");
				// Fusionne les traductions originales par celles modifiées à partir du CMS.
				$stack[$group] = array_replace_recursive(
					$this->loadJson($file->getPathname()),
					$this->loadJson($app->langOverridesPath("$group.$extension"))
				);
			}

			// Aplati la compile à un seul niveau de clés en "dot notation".
			$stack = Arr::dot($stack);

			// Sauvegarde la compile pour la langue courante.
			file_put_contents(
				$app->langCompiledPath("$locale.json"),
				json_encode($stack, JSON_UNESCAPED_UNICODE),
			);
		}
	}

	/**
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

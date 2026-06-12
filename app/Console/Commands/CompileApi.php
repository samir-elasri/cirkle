<?php

namespace App\Console\Commands;

use Arr;
use File;
use Illuminate\Console\Command;
use App\Foundation\Application;
use Symfony\Component\Finder\Finder;

class CompileApi extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'locales:api';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Compile API locales.';

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
		$stack = [];

		// Compile toutes les langues de l'application.
		foreach ($locales as $locale) {

			// Charge toutes les traductions de la langue courante.
			$extension = "$locale.json";

			foreach (Finder::create()->in(lang_path())->files()->name("api.$extension") as $file) {

				// Fusionne les traductions originales par celles modifiées à partir du CMS.
				$stack[$locale] = array_replace_recursive(
					$this->loadJson($file->getPathname()),
					$this->loadJson($app->langOverridesPath("api.$extension"))
				);
			}
		}


		$file_path = $app->basePath('translations_time.txt');

		if (is_file($file_path)) {
			$version = json_decode(file_get_contents($file_path), true, 512, JSON_THROW_ON_ERROR);
			file_put_contents($file_path, json_encode(++$version, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

		} else {
			$version = 1;
			file_put_contents($file_path, json_encode($version, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		}

		if(!File::isDirectory($app->apiCompiledPath())){
			File::makeDirectory($app->apiCompiledPath(), 0777, true, true);
		}

		// Sauvegarde la compile pour la langue courante.
		file_put_contents(
			$app->apiCompiledPath("translations.json"),
			json_encode($stack, JSON_UNESCAPED_UNICODE),
		);

		$this->info('Successfully compiled api translations : '. $app->apiCompiledPath("translations.json"));
	}

	/**
	 * @param  string  $filename
	 * @return array
	 * @noinspection JsonEncodingApiUsageInspection
	 */
	protected function loadJson(string $filename): array
	{
		return @json_decode(@file_get_contents($filename), true)
			?? [];
	}
}

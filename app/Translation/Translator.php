<?php

namespace App\Translation;

use Arr;
use Illuminate\Translation\Translator as BaseTranslator;
use Symfony\Component\Finder\Finder;

class Translator extends BaseTranslator
{
	/**
	 * Retourne tous les groupes de traductions.
	 *
	 * @return array
	 */
	public function getAllGroups(): array
	{
		$groups = [];
		$groupNamePattern = '/([^.]+?)\.(.+)\.json/';

		foreach (Finder::create()->in(lang_path())->files()->name("*.*.json") as $file) {
			$groups[] = preg_replace($groupNamePattern, '\1', $file->getBasename());
		}

		$groups = array_unique($groups);

		ksort($groups, SORT_NATURAL);

		return $groups;
	}

	/**
	 * Retourne les traductions d'un groupe.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function getGroupTranslations(string $name): array
	{
		$translations = [];

		foreach (getLocales() as $locale) {

			$filename = "$name.$locale.json";

			$hardcoded = Arr::dot($this->loadJson(lang_path($filename)));
			$overrides = Arr::dot($this->loadJson(public_path("lang/$filename")));

			$keys = array_keys(
				array_replace($hardcoded, $overrides)
			);

			foreach ($keys as $key) {
				$original = $hardcoded[$key] ?? null;
				$override = $overrides[$key] ?? null;
				$translations[$key][$locale][0] = $original;
				$translations[$key][$locale][1] = $override;
			}
		}

		return $translations;
	}

	/**
	 * @param  string  $filename
	 * @return array
	 * @noinspection JsonEncodingApiUsageInspection
	 */
	public function loadJson(string $filename): array
	{
		return @json_decode(@file_get_contents($filename), true)
			?? [];
	}
}

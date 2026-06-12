<?php

namespace App\Http\Controllers;

use Arr;
use GuzzleHttp\Exception\GuzzleException;
use Log;

trait ProfanityApiTrait
{

	protected function containsProfanity($text, $locale): bool
	{
		$url = 'https://api.sightengine.com/1.0/text/check.json';
		$body = [
			'text' => $text,
			'lang' => $locale,
			'mode' => 'rules',
			'api_user' => config('sightengine.user'),
			'api_secret' => config('sightengine.secret'),
		];

		$options = [];
		if (config('app.env') === 'local') {
			$options['verify'] = false;
		}

		try {
			$response = \Http::withOptions($options)
				->asForm()
				->post($url, $body)
				->json();

			return (bool) count(Arr::get($response, 'profanity.matches'));
		}
		catch (GuzzleException $e) {
			Log::error('sightengine checkForProfanity', $e);
		}

		return false;
	}
}

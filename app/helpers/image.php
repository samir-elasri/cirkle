<?php

use Intervention\Image\Constraint;
use Faker\Generator as Faker;

/**
 * Permet de mettre en cache une dimension spécifique d'une image à l'aide de Intervention\Image. La source doit se trouver sur le serveur et il faut spécifié au minimum spécifier un 'width' ou un 'height'.
 *
 * N.B. Ce répertoire est aussi vidé lorsque l'on fait vider le cache dans l'admin. (voir AdminAuthController@clearCache)
 *
 * @param  string|null  $source
 * @param  array  $params
 * @param  bool  $fromFacade
 * @return string
 */
function imageCache(?string $source, array $params, bool $fromFacade = false)
{
	$baseUrl = url('/');

	// Permet d'obtenir l'url relatif au serveur
	if (Str::startsWith($source, $baseUrl)) {
		$source = substr($source, strlen($baseUrl));
	}

	$source = ltrim($source, '/');
	$target = public_path($source);

	// Retourne la source si celle-ci est vide ou n'existe pas sur le serveur
	if (empty($source) || !file_exists($target)) {
		return $source;
	}

	$width = Arr::get($params, 'width');
	$height = Arr::get($params, 'height');
	$factor = Arr::get($params, 'factor', 1.25);
	$crop = Arr::get($params, 'crop');

	// Retourne simplement la source si aucune dimension n'est fournie
	if (!$width && !$height) {

		if ($fromFacade && config('app.debug')) {
			Session::flash('error',
				"Veuillez spécifier un paramètre de taille à l'image \"{$source}\" (Voir Html::image et imageCache)");
		}

		return $source;
	}

	$dot = strrpos($source, '.');
	$path = substr($source, 0, $dot);
	$ext = substr($source, $dot + 1);

	// Permet de cibler la dimension souhaitée
	$modifier = '-';
	$modifier .= $width ? "-w$width" : '';
	$modifier .= $height ? "-h$height" : '';
	$modifier .= $factor ? "-x$factor" : '';
	$modifier .= $crop ? '-c' : '';
	$modifier .= '_' . filemtime($target);

	// URI de l'image en cache
	$uri = sprintf('%s/%s%s.%s', config('image.cache'), $path, $modifier, $ext);

	$cache = public_path($uri);

	if (!file_exists($cache)) {

		File::makeDirectory(dirname($cache), 0755, true, true);

		$image = Image::make($source);
		$image->orientate();

		if ($crop) {
			// The following two lines is equivalent to finding if one value is null and setting it to the other
			$width = $width ?: $height;
			$height = $height ?: $width;

			$image->fit($width * $factor ?: null, $height * $factor ?: null, static function ($constraint) {
				/** @var Constraint $constraint */
				$constraint->upsize();
			});

		} else {

			$image->resize($width * $factor ?: null, $height * $factor ?: null, static function ($constraint) {
				/** @var Constraint $constraint */
				$constraint->aspectRatio();
				$constraint->upsize();
			});
		}

		$image->save($cache);
	}

	return $uri;
}

/**
 * FakerPhp's image() function without the ssl validation for use in LOCAL environment
 * when cacert.pem is not working
 *
 * @param  Faker  $faker
 * @param  null  $dir
 * @param  int  $width
 * @param  int  $height
 * @param  null  $category
 * @param  bool  $fullPath
 * @param  bool  $randomize
 * @param  null  $word
 * @param  false  $gray
 * @return false|RuntimeException|string
 */
function getTmpImage(
	Faker $faker,
	$dir = null,
	$width = 640,
	$height = 480,
	$category = null,
	$fullPath = true,
	$randomize = true,
	$word = null,
	$gray = false
) {
	$dir = $dir ?? sys_get_temp_dir(); // GNU/Linux / OS X / Windows compatible

	// Validate directory path
	if (!is_dir($dir) || !is_writable($dir)) {
		throw new \InvalidArgumentException(sprintf('Cannot write to directory "%s"', $dir));
	}

	// Generate a random filename. Use the server address so that a file
	// generated at the same time on a different server won't have a collision.
	$name = md5(uniqid(empty($_SERVER['SERVER_ADDR']) ? '' : $_SERVER['SERVER_ADDR'], true));
	$filename = $name . '.png';
	$filepath = $dir . DIRECTORY_SEPARATOR . $filename;

	$url = $faker->imageUrl($width, $height, $category, $randomize, $word, $gray);

	// save file
	if (function_exists('curl_exec')) {
		// use cURL
		$fp = fopen($filepath, 'w');
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$success = curl_exec($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
		fclose($fp);
		curl_close($ch);

		if (!$success) {
			unlink($filepath);

			// could not contact the distant URL or HTTP error - fail silently.
			return false;
		}
	} elseif (ini_get('allow_url_fopen')) {
		// use remote fopen() via copy()
		$success = copy($url, $filepath);
	} else {
		return new \RuntimeException('The image formatter downloads an image from a remote HTTP server. Therefore, it requires that PHP can request remote hosts, either via cURL or fopen()');
	}

	return $fullPath ? $filepath : $filename;
}


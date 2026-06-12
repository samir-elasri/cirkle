<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Foundation\Application;
use App\Models\Core\User;
use Artisan;
use Auth;
use Cache;
use File;
use Illuminate\Http\RedirectResponse;
use JsonException;
use Log;
use Redirect;
use Request;
use Response;
use Route;
use Session;
use Str;
use Validator;
use View;

class AdminAuthController extends AdminBaseController
{

	public function token()
	{
		return csrf_token();
	}

	public function getLogin()
	{
		return View::make('auth.login');
	}

	public function postLogin()
	{

		$data = Request::all();

		$validator = Validator::make($data, User::$auth_rules);
		if ($validator->fails()) {

			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}

		// Todo Doesn't require the user to be active?
		if (Auth::guard('users')
			->attempt([
				'email'    => Request::get('email'),
				'password' => Request::get('password')
			])) {

			/** @var User $user */
			$user = Auth::guard('users')
				->user();
			$user->previous_login = $user->updated_at; //date du login précédent
			$user->save();

			return Redirect::intended('/admin/dashboard');
		}

		Session::flash('error', 'Les informations saisies ne correspondent pas à nos dossiers');

		return Redirect::back()
			->withInput();
	}

	public function getLogout()
	{

		Auth::guard('users')
			->logout();

		return Redirect::route('admin.login');
	}

	public function getDashboard()
	{
		return View::make('generic.dashboard');
	}

	public function getFilesContainer()
	{
		return View::make('generic.filesContainer');
	}

	public function getTranslations()
	{
		return View::make('generic.translations');
	}

	public function getSmtp()
	{
		return View::make('generic.smtp');
	}

	public function setSmtp()
	{
		$data = Request::except([
			'_token',
			'locale'
		]);
		$file = App::environmentFilePath();
		$content = file_get_contents($file);

		$bools = [
			'VERIFY_PEER',
			'VERIFY_PEER_NAME',
			'ALLOW_SELF_SIGNED',
		];

		foreach ($data as $key => $datum) {
			if (Str::contains($datum, ' ')) {
				$datum = "\"$datum\"";
			}

			if (in_array($key, $bools, true)) {
				$datum = $datum === '1' ? 'true' : 'false';
			}

			$content = preg_replace(
				"/^$key=.*$/m",
				"$key=$datum",
				$content);
		}

		file_put_contents($file, $content);

		return Redirect::back()
			->with('success', 'Les paramètres ont bien été sauvegardées');
	}

	/**
	 * @throws JsonException
	 */
	public function translationUpdate(): RedirectResponse
	{
		/** @var $app Application */
		$app = app();

		$locales = getLocales();
		$group = Route::input('group');
		$after = Request::only(getLocales());

		foreach ($locales as $locale) {
			$filename = "$group.$locale.json";

			// Read the base translation file
			$base = [];
			if (is_file($app->langPath($filename))) {
				try {
					$base = json_decode(file_get_contents($app->langPath($filename)), true, 512, JSON_THROW_ON_ERROR);
				} catch (\Exception $e) {
					Log::error("Error reading base file $filename: " . $e->getMessage());
				}
			}

			// Calculate changes: only save differences from base translations
			$changes = array_changes($base, $after[$locale] ?? []);

			// Write to override file
			file_put_contents($app->langOverridesPath($filename), json_encode($changes, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		}

		// Run compilation commands
		Artisan::call('locales:compile');

		if ($group === 'api') {
			Artisan::call('locales:api');
		}

		return Redirect::back()
			->with('success', 'Les traductions ont bien été sauvegardées');
	}

	public function clearCache()
	{
		$cacheFile = base_path('cache-time.txt');
		$version = time();
		file_put_contents($cacheFile, $version);

		Cache::flush();
		@Artisan::call('cache:clear');
		@Artisan::call('locales:compile');

		// Vide le cache des images
		if ($cache = config('image.cache')) {
			File::deleteDirectory(public_path($cache), true);
		}

		return Redirect::back()
			->with('success', 'Le cache est maintenant vide');
	}

	public function download()
	{

		if (Request::has('file')) {

			$path_file = public_path() . urldecode(Request::get('file'));
			$mime = File::mimeType($path_file);
			$headers = array(
				'Content-Type: ' . $mime,
			);

			return Response::download($path_file, basename($path_file), $headers);
		}
	}

	public function changeDebug($debug)
	{
		if (is_admin()) {
			$app = app();
			$escaped = preg_quote('=' . (env('APP_DEBUG') ? 'true' : 'false'), '/');
			$before = "/^APP_DEBUG{$escaped}/m";

			$content = file_get_contents($app->environmentFilePath());

			$content = preg_replace(
				$before,
				'APP_DEBUG=' . $debug,
				$content
			);

			file_put_contents($app->environmentFilePath(), $content);
		}

		return redirect()->to('/admin/');
	}
}

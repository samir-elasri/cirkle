<?php /** @noinspection UnknownInspectionInspection */

use App\Models\Core\Bloc;
use App\Models\Core\Setting;
use App\Models\Core\Subscriber;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;

function describe($model): bool|array
{
	return DB::connection()->getPdo()->query('describe ' . $model->getTable())->fetchAll();
}

function createCustomPaginator($paginator): array
{
	$pages = [];
	for ($i = 1, $l = $paginator->getLastPage(); $i <= $l; $i++) {
		$pages[] = (object) [
			'url' => $paginator->getUrl($i), 'label' => $i, 'active' => ($i === $paginator->getCurrentPage())
		];
	}
	$prevPage = ($paginator->getCurrentPage() > 1) ? (object) ['url' => $paginator->getUrl($paginator->getCurrentPage() - 1)] : '';
	$nextPage = ($paginator->getCurrentPage() < $paginator->getLastPage()) ? (object) ['url' => $paginator->getUrl($paginator->getCurrentPage() + 1)] : '';
	return ['prev' => $prevPage, 'next' => $nextPage, 'pages' => $pages];
}

function curl_get($url): bool|string
{
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	$return = curl_exec($curl);
	curl_close($curl);
	return $return;
}

function is_collection($obj): bool
{
	return $obj instanceof Illuminate\Database\Eloquent\Collection;
}

function is_model($obj): bool
{
	return $obj instanceof Model;
}

function asset_with_version($path): string
{
	$cacheFile = base_path('cache-time.txt');

	if (is_file($cacheFile)) {
		$version = file_get_contents($cacheFile);
	} else {
		$version = time();
		file_put_contents($cacheFile, $version);
	}

	$path .= (Str::contains($path, '?') ? '&' : '?');

	return "{$path}v={$version}";
}

function in_admin(): bool
{
	return Request::segment(1) === 'admin';
}

function is_admin($is_mbiance = false): bool
{
	/** @var User $user */
	$user = Auth::guard('users')->user();
	return $user && (!$is_mbiance || $user->is_mbiance);
}

function logged_in(): bool
{
	return Auth::guard('subscribers')->check();
}

/**
 * @return Subscriber|null
 * @noinspection PhpIncompatibleReturnTypeInspection
 */
function current_user(): ?Subscriber
{
	return Auth::guard('subscribers')->user();
}

function mapChildren($children)
{
	if ($children->count()) {
		$first = $children->first();
		$grid = $first['grid'];
		$gridUtility = app('gridutility');


		$children = $children->map(static function ($child) use ($gridUtility, $grid) {
			/** @var Model $child */
			$values = $child->toArray();

			foreach ($grid as $field) {
				$name = is_array($field) ? implode('_', $field) : $field;
				$values[$name] = $gridUtility->getFieldDataStr($field, $child);
			}

			return $values;
		});
	}

	return $children;
}

function inaccessible(&$params)
{
	$params['view_name'] = 'pages.inaccessible';

	return $params;
}

function restricted(&$params)
{
	$params['view_name'] = 'core.partials.restricted';

	return $params;
}

if (!function_exists('setting')) {
	/**
	 * Permet de récupérer les paramètres de l'application.
	 *
	 * @param string|null $key
	 * @param mixed|null $default
	 * @return mixed|Setting
	 */
	function setting(string $key = null, mixed $default = null): mixed
	{
		if (is_null($key)) {
			return app('setting');
		}

		return app('setting')->$key
			?? $default;
	}
}

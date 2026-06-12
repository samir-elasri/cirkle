<?php

namespace Mbiance\AdminUtility;

use App\Models\Core\SideMenu;
use Request;
use Route;
use Str;

class RoutingUtility
{
	public $modelutility;

	/**
	 * RoutingUtility constructor.
	 *
	 * @param $modelutility ModelUtility
	 */
	public function __construct($modelutility)
	{
		$this->modelutility = $modelutility;
	}

	public function isAdmin()
	{
		return (Request::segment(1) === 'admin');
	}

	public function setEntityRoute($identifiant, $mode, $role = '*')
	{
		$role = (string) $role;
		$mode = (string) $mode;
		$addAuth = false;

		if (($mode == '') || ($identifiant === '')) {
			return;
		}

		// SI role truthy et pas *
		if ($role && $role != '*') {
			$addAuth = true;
		}

		$identifiant = (string) $identifiant; //pour mettre en string les SimpleXMlElement

		$class_name = config("classmap.$identifiant");

		$resourceName = Str::singular($identifiant);

		if (class_exists($class_name)) {

			$o = new $class_name();

			if (array_search('is_headline', $o['grid'])) {
				$r = Route::post($identifiant . '/setheadline',
					'AdminGenericController@setHeadline')->name("{$identifiant}.headline");
				if ($addAuth) {
					$r->middleware("role:{$role}");
				}
			}

			if ($o->order_default === 'position') {
				$r = Route::post($identifiant . '/order', 'AdminGenericController@order')->name("{$identifiant}.order");
				if ($addAuth) {
					$r->middleware("role:{$role}");
				}
			}

			if (isset($o['exports'])) {
				$r = Route::get($identifiant . '/export/{method}/{id?}/{parent?}', 'AdminGenericController@export')
					->name("{$identifiant}.export");
				if ($addAuth) {
					$r->middleware("role:{$role}");
				}
			}
		}

		$exception = [
			'show',
			'edit'
		]; //l'action show n'est pas implementé... -> possiblement si on doit mettre un form en readonly

		//Approche globale - on donne les autorisation si celles-ci sont spécifiées uniquement
		if (!str_contains($mode, 'C')) { // si "Create" n'est pas présent
			$exception[] = 'create';
			$exception[] = 'store';
		}

		if (!str_contains($mode, 'D')) { // si "Destroy" n'est pas présent
			$exception[] = 'destroy';
		}

		if (!str_contains($mode, 'U')) { // si "Destroy" n'est pas présent
			$exception[] = 'update';

		} else {

			Route::get("/$identifiant/" . '{' . "$identifiant}/edit/{onglet?}/{childId?}", [
				'as'   => "$identifiant.edit",
				'uses' => 'AdminGenericController@edit'
			]);
		}


		$r = Route::resource($identifiant, 'AdminGenericController', ['except' => $exception]);

		if ($addAuth) {
			$r->middleware("role:{$role}");
		}

		// add routes for recalculations
		$r = Route::put($identifiant . "/{{$resourceName}}/recalculate",
			'AdminGenericController@recalculate')->name("{$identifiant}.recalculate");
		if ($addAuth) {
			$r->middleware("role:{$role}");
		}
	}

	public function setEntityRoutesFromConfig()
	{

		$e = config('crud');

		if ($e) {
			foreach ($e as $key => $value) {

				$this->setEntityRoute($key, $value);
			}
		}
	}
}

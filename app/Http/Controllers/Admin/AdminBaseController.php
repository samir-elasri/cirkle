<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;
use ModelUtility;
use PageUtility;
use Redirect;
use Request;
use Route;
use Session;
use stdClass;
use Str;
use View;

class AdminBaseController extends BaseController
{

	protected $class_name;
	protected $collection_name;
	protected $model;

	public function __construct()
	{

		/**
		 * Path des vues par défaut
		 */
		$this->class_name = ModelUtility::getClassNameFromRoute();

		/**
		 *    Si Generic Controller, on identifie le model pour le controller
		 */
		if (Str::contains(Route::currentRouteAction(), 'AdminGenericController')) {

			if (class_exists($this->class_name)) {    //verification que la class existe...

				$this->collection_name = ModelUtility::getCollectionNameFromRoute();
				$this->model = new $this->class_name();
				Session::now('admin.model', $this->model);
			}

			//TRAITEMENT SI PAS AJAX
			if (!Request::wantsJson()) {
				if (
					(Str::contains(Route::currentRouteAction(),
						'@index')) || (Str::contains(Route::currentRouteAction(),
						'@edit')) || (Str::contains(Route::currentRouteAction(), '@create'))
				) {

					if ($this->collection_name === 'settings') { //CAS PARTICULIER SETTINGS
						$entite = new stdClass();
						$entite->nom = 'paramètres';
						$entite->collection = 'settings';
						$entite->isPermissionUpdate = true;
						$entite->isPermissionCreate = false;
						$entite->isPermissionDelete = false;
						$entite->icone = 'fa-cogs';
					} else {
						$entite = PageUtility::getElementMenu($this->collection_name);
					}

					if ($entite) {
						View::share('entite', $entite);
						View::share('isCreate', PageUtility::isCreate());
					}
				}
			}
		}
	}
}

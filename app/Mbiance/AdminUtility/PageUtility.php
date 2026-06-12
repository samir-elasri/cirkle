<?php

namespace Mbiance\AdminUtility;

use App\Models\Core\Model;
use App\Models\Core\SideMenu;
use Arr;
use File;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use ModelUtility as ModelUtilityFacade;
use Request;
use Route;
use Session;
use Str;
use SimpleXMLElement;

class PageUtility
{

	public $modelutility;

	/**
	 * PageUtility constructor.
	 *
	 * @param $modelutility ModelUtility
	 */
	public function __construct($modelutility)
	{
		$this->modelutility = $modelutility;
	}

	public function getElementMenu($collection_name)
	{
		$menu = SideMenu::find($collection_name);

		foreach (SideMenu::getAll() as $item) {
			if ($item->SideMenuItems->count() > 0) {
				foreach ($item->SideMenuItems->SideMenuItem as $subItem) {
					if ($subItem->Identifiant == $collection_name) {
						$menu = $subItem;
					}
				}
			}
		}

		if (!$menu) {

			$mode = Arr::get(config('crud'), $collection_name);

			$menu = new SimpleXMLElement('<xml></xml>');

			$menu->collection = $collection_name;
			$menu->isPermissionCreate = Str::is('C*', $mode);
			$menu->isPermissionUpdate = Str::is('*U*', $mode);
			$menu->isPermissionDelete = Str::is('*D', $mode);
		} else {

			$menu->nom = mb_strtolower($menu->Nom);
			$menu->collection = $menu->Identifiant;

			if (!isset($menu->Singulier)) {
				$menu->Singulier = 'un item';
			}

			if (!isset($menu->Regroupement)) {
				$menu->Regroupement = $menu->nom;
			}

			$menu->isPermissionCreate = Str::is('C*', $menu->Mode);
			$menu->isPermissionUpdate = Str::is('*U*', $menu->Mode);
			$menu->isPermissionDelete = Str::is('*D', $menu->Mode);
			$menu->icone = $menu->Icone;
		}

		return $menu;
	}

	public function itemHasPermissionCreate()
	{

		$onglet = Route::input('onglet');

		$permissions = config('crud.' . $onglet, '');

		return Str::is('C*', $permissions);
	}

	public function checkIfChildrenSelected($m)
	{

		$collection_name = Request::segment(2);
		$menu = SideMenu::find($collection_name);

		if (!$menu) {

			$currentParent = '';

			foreach (SideMenu::getAll() as $item) {

				if ($item->SideMenuItems->count() > 0) {
					if ($m->Identifiant == $item->Identifiant) //même noeud que celui interrogé
					{
						foreach ($item->SideMenuItems->SideMenuItem as $item) {

							if ($item->Identifiant == $collection_name) {
								return $this->getUrlDestination($item);
							}
						}
					}
				}
			}
		}

		return false;
	}

	public function getUrlDestination($menu)
	{

		return (Request::segment(2) == $menu->Identifiant);
	}

	/**
	 * @return string
	 */
	public function getGridPath()
	{

		$entite = Request::segment(2);

		$view_path = resource_path("views/_admin/grid/{$entite}.blade.php");

		if (File::exists($view_path)) { //vérifier si vue custom
			return '_admin.grid.' . $entite;
		}

		//si non - solution générique du framework
		$generic_grid_path = app_path("Mbiance/Views/grid/{$entite}.blade.php");

		if (File::exists($generic_grid_path)) {
			return 'grid.' . $entite;
		}

		$class_name = ModelUtilityFacade::getClassByCollectionName($entite);
		$model = new $class_name();

		if ($model->order_default === 'position') {

			return 'widgets.topgrid';
		}

		return 'generic.grid';
	}

	public function getFormPath()
	{

		$routes = Request::segment(2);
		$view_path = resource_path("views/_admin/{$routes}/form.blade.php");

		if (File::exists($view_path)) {
			return '_admin.' . $routes . '.form';
		}

		return 'generic.form';
	}

	public function getOngletPath($onglet)
	{

		$view_path = resource_path("views/_admin/forms/{$onglet}.blade.php");

		if (File::exists($view_path)) {
			return '_admin.forms.' . $onglet;
		}

		return 'generic.form_general';
	}

	public function getWidgetPath($widget_name)
	{

		$widget_path = resource_path("views/_admin/widgets/{$widget_name}.blade.php");

		if (File::exists($widget_path)) {
			return '_admin.widgets.' . $widget_name;
		}

		return 'widgets.' . $widget_name;
	}

	public function getOnglet()
	{

		$entite = Request::segment(2);
		$childId = Route::input('childId');

		/** @var HasMany|BelongsToMany|HasOne $relationObj */
		if ($relationObj = Session::get('admin.relationObj')) {
			switch (true) {

				case $relationObj instanceof HasMany:
				case $relationObj instanceof MorphMany:
				case $relationObj instanceof BelongsToMany:
					if ($childId) {
						return 'generic.form_subgrid';
					}

					return 'widgets.subgrid';

				case $relationObj instanceof HasOne:
				case $relationObj instanceof MorphOne:
					return 'generic.subform';

				default:

			}
		}

		$suffixe = ($entite . '-general');

		return $this->getOngletPath($suffixe);
	}

	public function isCreate()
	{

		return (Request::segment(3) == 'create');
	}

	public function getViewTrans($view, $element)
	{

		return __(str_replace('.', '/', $view) . '.' . $element);
	}

	public function addItemLink()
	{
		/** @var HasMany|BelongsToMany|HasOne $relationObj */
		if ($this->itemHasPermissionCreate() && Session::has('admin.relationObj')) {
			$collection = Request::segment(2);
			$id = Route::input($collection);
			$onglet = Route::input('onglet');

			$url = adminRouteName("admin.$collection.edit", [
					$collection => $id,
					'onglet'    => $onglet
				]) . '/create';

			return "<a class='btn btn-primary' href='$url'><i class='fa fa-plus'></i> " . $this->modelutility->getAddItemLabel() . '</a>';
		}

		return '';
	}

	public function backToListLink()
	{
		if (Session::get('admin.relationObj')) {
			$urlArr = explode('/', Request::url());
			array_pop($urlArr);
			$url = implode('/', $urlArr);

			return "<a class='btn btn-inverse' href='$url'><i class='fa fa-chevron-left'></i> " . $this->modelutility->getBackToListLabel() . '</a>';
		}

		return '';
	}

	public function addExport()
	{
		$id = Request::segment(3);

		/** @var HasMany|BelongsToMany|HasOne $relationObj */
		if ($relationObj = Session::get('admin.relationObj')) {
			/** @var Model $class */
			$class = $relationObj->getRelated();

			if ($class->isSubgridExportable()) {
				$arr = Arr::get($class['exports'], 'generic');
				$entity = Str::snake(Str::pluralStudly(class_basename($class)));

				$url = adminRouteName("admin .{$entity}.export", [$arr['method'], $id, Request::segment(2)]);
				return '<a href="' . $url . '" class="btn ml - auto btn - sm btn - success"><i class="fa fa - download"></i> Export</a>';
			}
		}

		return '';
	}
}

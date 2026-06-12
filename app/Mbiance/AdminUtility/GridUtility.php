<?php /** @noinspection AllyPlainPhpInspection */

namespace Mbiance\AdminUtility;

use App\Models\Core\Model;
use App\Models\Core\Page;
use App\Models\Core\User;
use Arr;
use Auth;
use Carbon\Carbon;
use File;
use Form;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\HtmlString;
use Request;
use Session;
use Str;

/**
 * Class GridUtility
 *
 * @property Model $model
 * @property string $model_name
 * @property string collection_name
 * @property array fields
 * @property PageUtility $pageutility
 * @property ModelUtility $modelutility
 *
 * @package Mbiance\AdminUtility
 */
class GridUtility
{
	public $pageutility;

	public $modelutility;

	private $model;

	private $fields = array();

	private $collection_name;

	private $menu;

	/**
	 * GridUtility constructor.
	 *
	 * @param PageUtility $pageutility
	 * @param ModelUtility $modelutility
	 */
	public function __construct($pageutility, $modelutility)
	{
		$this->pageutility = $pageutility;
		$this->modelutility = $modelutility;
	}

	/**
	 * @param Model $model
	 */
	public function setGrid($model): void
	{
		$this->model = $model;
		$this->collection_name = $model->collection_name;
		$this->fields = $model['grid'];
		$this->menu = $this->pageutility->getElementMenu($this->collection_name);
	}

	public function getModel()
	{
		return $this->model;
	}

	public function normalizeField($field)
	{
		if (is_array($field)) {
			return $field;
		}

		if (str_contains($field, ':')) { //si type spécifié
			$t = explode(':', $field, 2);
			if (isset($t[0])) {
				$field = $t[0];
			}
		}
		return $field;
	}

	/**
	 * @param $field
	 * @param bool $topgrid
	 * @return string
	 */
	public function getHeader($field, $topgrid = false): string
	{

		$str = '';

		$field = $this->normalizeField($field);

		$field = is_array($field) ? implode('_', $field) : $field;

		if (strpos($field, '.') > -1) {

			$i = Arr::get($this->model['niceNames'], $field);

			if ($i === null) {
				$elem = explode('.', $field);

				if ($elem[1] !== 'count') {
					$field = Str::snake($elem[0]) . '_id';
				}
			}
		}

		$i = Arr::get($this->model['niceNames'], $field);


		$className = $topgrid ? '' : 'sortable sorting';
		//$className = "";
		$onClick = '';

		if ($i !== null) {
			$str .= "<th data-field=\"{$field}\" class=\"{$className}\" onclick=\"{$onClick}\">{$i}</th>";
		} elseif ((Str::contains($field, '_fr'))) {
			$element = str_replace('_fr', '', $field);
			$str .= '<th data-field="' . $field . '" class="' . $className . '" onclick="' . $onClick . '">' . __("properties.$element") . ' (fr)</th>';
		} elseif ((Str::contains($field, '_en'))) {
			$element = str_replace('_en', '', $field);
			$str .= '<th data-field="' . $field . '" class="' . $className . '" onclick="' . $onClick . '">' . __("properties.$element") . ' (en)</th>';
		} else {
			$str .= '<th data-field="' . $field . '" class="' . $className . '" onclick="' . $onClick . '">' . __("properties.$field") . '</th>';
		}

		return $str;
	}

	public function showGrid(): string
	{

		$useDataTables = !$this->model['bigData'];
		$model_name = get_class($this->model);
		if (Session::has('grid-state') && $model_name !== Session::get('grid-state.model')) {
			Session::pull('grid-state');
		}

		$str = $this->addGenericExport();
		$str .= $this->addItem();
		$key = array_search($this->model->order_default, $this->fields, true);
		$order_index = 0;
		if ($key) {
			$order_index = $key;
		}

		if (!$useDataTables) {

			$str .= '<div class="row"><div class="col-lg-6"><div class="dataTables_length" id="DataTables_Table_0_length"><label>Afficher
                    <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select> éléments</label></div></div>';
			// Filter
			$str .= '<div class="col-lg-6">
                        <div id="DataTables_Table_0_filter" class="dataTables_filter">
                            <label>Rechercher&nbsp;:&nbsp;
                                <input type="search" name="search" />
                            </label>
                        </div>
                    </div>';
			$str .= '</div>';
		}

		$str .= '<table data-length="'
			. Session::get('grid-state.data.length', 25)
			. '" data-search="'
			. Session::get('grid-state.data.search', '')
			. '" data-page="'
			. Session::get('grid-state.data.page', 1)
			. '" data-model="' . get_class($this->model)
			. '" data-sort="'
			. Session::get('grid-state.data.sort', $this->model->order_default)
			. '" data-order="'
			. Session::get('grid-state.data.order', $this->model->order_direction)
			. '" class="table table-striped table-bordered bootstrap-datatable '
			. ($useDataTables ? 'datatable ' : 'bigdata')
			. '"data-order-index="'
			. $order_index
			. '" data-sort-order="'
			. mb_strtolower($this->model->order_direction)
			. '">';

		$str .= '<thead>';
		foreach ($this->fields as $field) {
			$str .= $this->getHeader($field);
		}

		if ($this->isManageOrdre()) {
			$str .= '<th></th>';
		}

		$str .= '<th style="width:120px;">Actions</th>';
		$str .= '</thead>';

		if (!$useDataTables) {

			$str .= $this->getData(
				Session::get('grid-state.model'),
				Session::get('grid-state.data.page'),
				Session::get('grid-state.data.length'),
				Session::get('grid-state.data.sort'),
				Session::get('grid-state.data.order'),
				Session::get('grid-state.data.search')
			);
		} else {
			$str .= $this->getData();
		}

		$str .= '</table>';

		if (!$useDataTables) {

			// Pagination
			$str .= '<div class="col-lg-12"><div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">Affichage de l\'élement %start% à %end% sur %total% élément(s)</div></div>';
			$str .= '<div class="col-lg-12 center"><div class="dataTables_paginate paging_bootstrap" id="DataTables_Table_0_paginate"><ul class="pagination">';

			// Prev and first button
			$str .= '<li class="prev"><a href="#">← Précédent</a></li>';
			$str .= '<li class="first"><a href="#">Premier</a></li>';

			// Next and last button
			$str .= '<li class="last"><a href="#">Dernier</a></li>';
			$str .= '<li class="next"><a href="#">Suivant → </a></li>';

			$str .= '</ul></div></div>';
		}

		return $str;
	}

	/**
	 * @param null|string|array $model
	 * @param null|string|int $page
	 * @param null|string|int $length
	 * @param null|string $sort
	 * @param null|string $order
	 * @param null|string $search
	 * @return string
	 */
	public function getData(
		$model = null,
		$page = null,
		$length = null,
		$sort = null,
		$order = null,
		$search = null
	): string
	{

		$relation = null;
		$submodel = null;
		$relationObj = null;

		if (is_string($model)) {
			$this->setGrid(new $model);

		} elseif (is_array($model)) {
			[$model, $relation] = $model;

			/** @var Model $submodel */
			$relationObj = Session::get('admin.relationObj');

			if ($relationObj instanceof BelongsToMany) {
				$submodel = $relationObj->newPivot();
			} else {
				$submodel = $relationObj->getRelated();
			}

			$this->setGrid($submodel);

			if (
				$this->model->order_default === 'position'
				&&
				count($this->model->positionParentFields) === 0
			) {
				Session::now('error',
					'Veuillez spécifiez les champs parent du modèles pour la mise a jour des positions (positionParentFields)');
			}

			$this->model['bigData'] = false;
		}

		if ($page === null) {
			$page = 1;
		}
		if ($length === null) {
			$length = 25;
		}
		if ($sort === null) {
			$sort = $this->model->order_default;
		}

		if ($order === null) {
			$order = $this->model->order_direction;
		}

		if ($relation) {
			if ($relationObj instanceof BelongsToMany) {
				$collections = $submodel::where($relationObj->getForeignPivotKeyName(), Request::segment(3))
					->get();
			} else {
				$collections = $this->model['bigData'] ? $submodel::getRange(
					$page,
					$length,
					$sort,
					$order,
					$search,
					$model,
					$relation
				) : $model->$relation()->get();

			}
		} else if ($this->model instanceof Page) {
			$collections = Page::get();
		} else {
			$collections = $this->model['bigData'] ? $this->model::getRange(
				$page,
				$length,
				$sort,
				$order,
				$search
			) : $this->model->get();
		}

		$str = '<tbody' . ($this->model['bigData'] ? ' data-total="' . $collections->total . '"' : '') . '>';

		/** @var Model[] $collections */
		foreach ($collections as $collection) {

			$str .= '<tr>';

			foreach ($this->fields as $field) {
				$str .= '<td>' . $this->getFieldDataStr($field, $collection) . '</td>';
			}

			$str .= '<td class="col-md-2">';

			$str .= $this->exportBtn($collection->id);


			if (!empty($collection->identifier) && in_array(
					$this->collection_name,
					['category_groups', 'categories']
				)) {
				// Catégories
				$str .= $this->editBtn($collection->id);

			} elseif ($collection instanceof Page && $collection->integrated) {
				// Page intégrée
				if ((int)$this->menu->isPermissionUpdate) {
					$url = adminRouteName("admin.$this->collection_name.edit",
						[($collection->id ?: $collection->label)]);
					$str .= '<a href="' . $url . '" class="btn btn-info"><i class="fa fa-edit"></i></a> ';
				}

			} else {
				$str .= $this->editBtn($collection->id, $relation);
				$str .= $this->deleteBtn($collection->id, $collection->getLabel());
			}

			if (method_exists($collection, $methodName = 'renderGridBtns')) {
				$str .= $collection->$methodName();
			}

			$str .= '</td>';

			$str .= '</tr>';
		}

		$str .= '</tbody>';
		return $str;
	}

	/**
	 * @param $model
	 * @param $field
	 * @return string
	 */
	public function getRelationField($model, $field): string
	{
		$str = ''; // Default answer

		$table = $model->getTable();
		[$relationName, $column] = explode('.', $field, 2);

		if ($table === $relationName) {
			return $this->getFieldDataStr($column, $model);
		}

		// if the relation exists
		$relation = $model->$relationName();

		if ($column === '?') {
			$isActive = false;

			$obj = $model->$relationName;
			if ($obj instanceof Model) {
				$isActive = true;
			} elseif ($obj instanceof Collection) {
				$isActive = $obj->count() > 0;
			}

			return $this->getCheck($isActive);
		}

		if ($relation instanceof HasMany) {
			if ($column === 'count') {
				if ($model->relationLoaded($relationName)) {
					return $model->$relationName->count();
				}

				return $model->$relationName()->count();
			}

			if (str_contains($column, 'latest.')) {
				/** @var Model $relationModel */
				$relationModel = $relation->latest()->first();
				[, $latestColumn] = explode('.', $column, 2);

				if (isset($relationModel)) {
					if (mb_stripos($latestColumn, '.') !== false) {
						return $this->getRelationField($relationModel, $column);
					}

					$relCollectionName = $relationModel->collection_name;

					$str =  $this->getFieldDataStr($latestColumn, $relationModel);

					if (array_key_exists($relCollectionName, config('crud')) && Str::contains(config("crud.$relCollectionName"), 'RU')) {
						$url = adminRouteName("admin.{$relCollectionName}.edit", [$relationModel->id]);
						return "<a href='$url'>$str</a>";
					}

					return $str;
				}
			} elseif (str_contains($column, 'each.')) {
				[, $labelColumn] = explode('.', $column, 2);
				return $model->$relationName->pluck($labelColumn)->implode(', ');
			}

		} else {

			$relationModel = $model->{$relationName};

			if (isset($relationModel)) {
				if (mb_stripos($column, '.') !== false) {
					$str = $this->getRelationField($relationModel, $column);

				} else {
					$relCollectionName = $relationModel->collection_name;

					$str =  $this->getFieldDataStr($column, $relationModel);

					if (array_key_exists($relCollectionName, config('crud')) && Str::contains(config("crud.$relCollectionName"), 'RU')) {
						$url = adminRouteName("admin.{$relCollectionName}.edit", [$relationModel->id]);
						return "<a href='$url'>$str</a>";
					}

					return $str;
				}
			}
		}

		return $str;
	}

	/**
	 * @return string|null
	 */
	public function addItem(): ?string
	{
		if ((int)$this->menu->isPermissionCreate) {
			if (!($this->model instanceof User) || (Auth::guard('users')->user()->admin && $this->model instanceof User)) {
				$url = adminRouteName("admin.$this->collection_name.create");

				return '<p><a href="' . $url . '" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> '
					. __(
						'admin.additem',
						['attr' => $this->menu->Singulier]
					) . '</a></p>';
			}
		}

		return '';
	}

	/**
	 * @return string|void
	 */
	public function addGenericExport()
	{
		if (isset($this->model['exports'])) {
			$arr = Arr::get($this->model['exports'], 'generic');

			if ($arr) {
				$url = adminRouteName("admin.$this->collection_name.export", [$arr['method']]);

				return '<p><a href="' . $url . '" class="btn pull-right btn-sm btn-success"><i class="fa fa-download"></i> ' . $arr['label'] . '</a></p>';
			}
		}

		return null;
	}

	/**
	 * @param $isActive
	 * @return string
	 */
	public function getStatus($isActive): string
	{
		$str = '<span class="label label-success">actif</span>';

		if (!$isActive) {
			$str = '<span class="label label-default">Inactif</span>';
		}

		return $str;
	}

	/**
	 * @param $isActive
	 * @return string
	 */
	public function getCheck($isActive): string
	{

		return $isActive ? '<i class="fa fa-check"></i>' : '';
	}

	/**
	 * @param $date
	 * @return string
	 */
	public function isNew($date): string
	{
		return ($date > Auth::guard('users')->user()->previous_login) ? ' &nbsp;<span class="tag label label-danger light"><i class="fa fa-check-circle-o"></i> nouveau</span>' : '';
	}

	/**
	 * @param $id
	 * @param null $label
	 * @return HtmlString|string
	 */
	public function deleteBtn($id, $label = null)
	{

		if ((int)$this->menu->isPermissionDelete) {
			$url = adminRouteName("admin.$this->collection_name.destroy", [$id]);

			return Form::button(
				'<i class="fa fa-trash-o"></i>',
				array(
					'type'    => 'submit',
					'onclick' => 'showConfirmDeleteModal(
                     \'' . addslashes($label) . '\',
                     \'' . url($url) . '\'
                    )',
					'class'   => 'btn btn-danger'
				)
			);
		}
		return '';
	}

	/**
	 * @param $id
	 * @param null $relation
	 * @return string
	 */
	public function editBtn($id, $relation = null): string
	{
		if ((int)$this->menu->isPermissionUpdate) {
			if ($relation) {
				$url = Request::fullUrl() . '/' . $id;
			} else {
				$name = Str::snake($this->collection_name);
				$url = adminRouteName("admin.$name.edit", [$id]);
			}

			return '<a href="' . $url . '" class="btn btn-info"><i class="fa fa-edit"></i></a> ';
		}

		return '';
	}

	/**
	 * @param $id
	 * @return string
	 */
	public function exportBtn($id): string
	{
		$html = '';

		if (isset($this->model['exports'])) {

			foreach ($this->model['exports'] as $key => $element) {

				if ($key !== 'generic') {
					$url = adminRouteName("admin.$this->collection_name.export", [$element['method'], $id]);

					$html .= '<a href="' . $url . '" class="btn btn-success" title="' . $element['label'] . '"><i class="fa fa-download"></i></a> ';
				}
			}
		}

		return $html;
	}

	/**
	 * SERT AUTANT POUR LE 1ER NIVEAU (TOPGRID) QU'AUX ENFANTS (SUBGRID)
	 *
	 * @param string $collection
	 * @param bool $topgrid
	 * @return string
	 */
	public function getHeaderFields($topgrid = false): string
	{
		/** @var HasMany|BelongsToMany|HasOne $relationObj */
		if ($relationObj = Session::get('admin.relationObj')) {
			if ($relationObj instanceof BelongsToMany) {
				$model = $relationObj->newPivot();
			} else {
				$model = $relationObj->getRelated();
			}
		} else {
			$model = Session::get('admin.model');
		}

		$this->setGrid($model);

		return implode(
			'',
			array_map(function ($field) use ($topgrid) {
				return $this->getHeader($field, $topgrid);
			}, $this->fields)
		);
	}

	/**
	 * @return bool
	 */
	public function isHeadline(): bool
	{

		return in_array('is_headline', $this->fields, true);
	}

	/**
	 * @return bool
	 */
	public function isManageOrdre(): bool
	{

		return $this->model->order_default === 'position';
	}

	/**
	 * @return string
	 */
	public function setValueFields(): string
	{

		$str = '';

		foreach ($this->fields as $field) {

			$str .= $this->getAngularFieldSetup($field);
		}

		return $str;
	}

	/**
	 * @param $field
	 * @return string
	 */
	public function getAngularFieldSetup($field): ?string
	{
		$field = is_array($field) ? implode('_', $field) : $field;

		if (($field === 'is_headline') || ($field === 'isWinner')) {
			return '<td><i class="fa fa-check" data-ng-show="item.' . $field . '" style="font-size:36px;"></i></td>';
		}

		if ($field === 'active' || $field === 'brand_active') {
			return "<td ng-bind-html='item." . $field . "'></td>";
		}

		if (str_contains($field, 'price_') || str_contains($field, '_price')) {
			return "<td><% item.{$field} | number:2 %> \$</td>";
		}

		if ($field === 'color') {
			return "<td><span class=\"label\" style=\"background:<% item.{$field} %>\"><% item.{$field} %></span></td>";
		}

		return "<td ng-bind-html='item." . $field . "'></td>";
	}

	public function getManagingMessage($entite)
	{

		$label = $entite->Regroupement ?? $entite->nom;

		$det = $entite->Determinant ?? __('admin.det');

		return __('admin.managing', ['det' => $det, 'attr' => $label]);
	}

	public function getListingMessage($entite)
	{

		$label = $entite->Regroupement ?? $entite->nom;

		$det = $entite->Determinant ?? __('admin.det');

		return __('admin.listing', ['det' => $det, 'attr' => $label]);
	}

	/**
	 * @param $field
	 * @param Model $collection
	 * @return string
	 */
	public function getFieldDataStr($field, $collection): string
	{
		$type = $this->model->getFieldType($field);
		$field = $this->normalizeField($field);
		$str = '';

		if (str_ends_with($field, '_color')) {
			$str .= '<div style="border-radius:100%;width:1em;height:1em;background-color:'.$collection->$field.'"></div>';
		} elseif (is_array($field)) {
			// If field is an array, concat all values with commas
			$arr = [];
			foreach ($field as $item) {
				$arr[] = $collection->$item;
			}
			$str = implode(' ', $arr);

		} elseif (is_array($collection->$field)) {
			// If field is an array, concat all values with commas
			$str = implode(', ', $collection->$field);

		} elseif ($field === 'active') {
			// If field is active column, find and render the status
			$str = $this->getStatus($collection->$field);

		} elseif ($field === 'thumb' || $field === 'image' || str_contains($field, '_image')) {

			if (urlencode($collection->$field)) {
				$str = "<img class='preview' src='{$collection->$field}' alt=''>";
			}

		} elseif ($field === 'document') {
			// If the field is named document, render a download button
			$str_download = '';

			if ($collection->$field) {

				if (File::exists(public_path() . $collection->$field)) {
					$str_download = '<a href="/admin/download?file=' . urlencode($collection->$field) . '" class="btn btn-info" title="télécharger"><i class="fa fa-download"></i></a>';

				} else {
					$isRestricted = (array_key_exists(
						$field,
						$this->model['medias']
					)) ? ($this->model['medias'][$field]['restricted'] ?? false) : 'false';

					if ($isRestricted) {
						$str_download = '<a href="' . $collection->$field . '" class="btn btn-info" title="télécharger"><i class="fa fa-download"></i></a>';
					}
				}
			}

			$str = $str_download;

		} elseif ($field === 'slug') {
			// If the field is names slug, show an url if present in the model
			$str = $collection->url;

		} elseif ($type === 'money') {
			// if the type of the field is money, format the value as a currency
			$str = prettyPrice($collection->$field);

		} elseif (($type === 'bool') || (strpos($type, 'tinyint') > -1)) {
			// If the type is boolean or tinyint, render a checkbox
			$str = $this->getCheck($collection->$field);

		} elseif (strpos($field, '.') > -1) {
			// If the type has unsigned in it or a dot, consider it a relation and show either count or attribute or relation
			$str = $this->getRelationField($collection, $field);

		} elseif (strpos($type, 'datetime') > -1) {
			// If the type is a datetime, format it

			if ($collection->$field !== null) {
				if ($collection->$field instanceof Carbon) {
					$str = $collection->$field->format('Y-m-d H:i:s');

				} elseif (is_object($collection->$field)) {
					$str = date('Y-m-d H:i:s', strtotime($collection->$field->date));

				} else {
					$str = date('Y-m-d H:i:s', strtotime($collection->$field));
				}
			}

		} elseif (strpos($type, 'date') > -1) {
			// If the type is a date, format it

			if ($collection->$field !== null) {
				if ($collection->$field instanceof Carbon) {
					$str = $collection->$field->format('Y-m-d H:i:s');

				} elseif (is_object($collection->$field)) {
					$str = date('Y-m-d', strtotime($collection->$field->date));

				} else {
					$str = date('Y-m-d', strtotime($collection->$field));
				}
			}

		} elseif ($type === 'timestamp') {
			// If the type is timestamp, print it as such. If it is created_at, add new if has been creted since last current user login.

			// $str .= date("Y-m-d", strtotime($collection->$field));
			$str = $collection->$field;

			if ($field === 'created_at') {
				$str .= $this->isNew($collection->$field);
			}

		} elseif (Arr::get($collection['enum'], $field)) {
			// If the field is present in enum, transform it into it's enum value

			$j = Arr::get($collection['enum'], $field);

			if ($j && ($key = $collection->$field)) {

				$value = Arr::get($j, $key, '');
				$str = $value;
			}

		} else {
			// Otherwise just print it as such

			$str = $collection->$field;
		}

		return $str ?? '';
	}
}

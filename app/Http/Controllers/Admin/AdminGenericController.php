<?php

namespace App\Http\Controllers\Admin;

use App;
use Arr;
use FormUtility;
use GridUtility;
use PageUtility;
use Redirect;
use Request;
use Response;
use Session;
use Str;
use View;

class AdminGenericController extends AdminBaseController
{

	public function index()
	{

		Session::now('admin.model', $this->model);
		GridUtility::setGrid($this->model);
		return View::make(PageUtility::getGridPath());
	}

	public function create()
	{
		FormUtility::setModel($this->model);
		return View::make(PageUtility::getFormPath(), ['model' => $this->model]);
	}

	public function store()
	{
		if (Request::wantsJson()) {
			return $this->model->saveElement(Request::all());
		}

		if ($this->model->saveElement(Request::all())) {

			if ($this->model->backAfterChange) { //retour à edit
				if ($url = Request::get('current_url')) {

					$url = Str::replace('create', $this->model->getLastCreatedId(), $url);
					return Redirect::to($url)->with(
						'success',
						__('admin.saveSuccess')
					);
				}


				return Redirect::to(adminRouteName('admin.' . $this->collection_name . '.edit', [$this->model->getLastCreatedId()]))
					->with('success', __('admin.saveSuccess'));
			}

			return Redirect::route('admin.' . $this->collection_name . '.index')->with(
				'success',
				__('admin.saveSuccess')
			);
		}

		return Redirect::back()->withErrors($this->model->errors)->withInput();
	}

	public function edit($id, $relation = null, $childID = null)
	{
		$relation = Str::camel($relation);

		if ($this->collection_name === 'pages') {

			if (is_numeric($id)) {
				$model = $this->model->find($id);

			} else {
				$model = $this->model->whereIntegrated(true)->whereLabel($id)->first();

				if (!$model) {
					// Récupère les paramètres de la page intégrée
					$config = config('routes.front-end');
					$route = Arr::get($config, $id, []);
					$params = Arr::get($route, 'page', []);

					$params['label'] = $id;
					$params['integrated'] = true;
					$params['active'] = true;

					$model = new $this->model();
					$model->saveElement($params);

					return Redirect::to(adminRouteName("admin.pages.edit", [$model->id]));
				}
			}

		} else {
			$model = $this->model->find($id);
		}

		Session::now('admin.model', $model);
		Session::now('admin.relationObj', $relation ? $model->$relation() : null);
		FormUtility::setModel($model, $relation, $childID);
		GridUtility::setGrid($model);

		return View::make(PageUtility::getFormPath(), compact('model'));
	}

	public function update($id)
	{

		$this->model = $this->model->findOrFail($id); //pas fin car on ne veut pas des relations lors des manipulation de données
		$modelSaved = $this->model->saveElement(Request::all());

		if (Request::wantsJson()) {
			if (!$this->model->isAjaxEnabled) {
				App::abort(401);
			} //verification si autorisé à faire des requêtes ajax

			if ($modelSaved) {
				// return Response::json(array('success' => true, 'last_insert_id' => $this->model->id), 200);
				return Response::json('L\'élément a été correctement modifié!', 200);
			}

			App::abort(500);
		}

		if ($modelSaved) {

			if ($this->model->backAfterChange) { //retour à sur la page d'origine après le changement
				return Redirect::back()->with('success', __('admin.saveSuccess'));
			}

			return Redirect::route('admin.' . $this->collection_name . '.index')->with(
				'success',
				__('admin.saveSuccess')
			);
		}

		return Redirect::back()->withErrors($this->model->errors)->withInput();
	}

	public function recalculate($id)
	{

		$this->model = $this->model->findOrFail($id); //pas fin car on ne veut pas des relations lors des manipulation de données

		if ($this->model->canRecalculate) {
			return Redirect::back()
				->withInput($this->model->recalculate())
				->withErrors($this->model->errors);
		}

		return Redirect::back()->withErrors('L\'option recalcul ne semble pas activée sur cette entité ou, elle ne possède pas une fonction de recalcul');
	}

	public function destroy($id)
	{

		$result = $this->model->destroy($id);

		if (Request::wantsJson()) {

			if ($result === 0) {
				App::abort(422);
			}

			// return Response::json(array('code' => 200), 200);
			return Response::json('L\'élément a été correctement supprimé!', 200);
		}

		return Redirect::back()->with('success', __('admin.deleteSuccess'));
	}

	public function order()
	{

		if (Request::wantsJson()) {
			$status = $this->model->changeOrder(Request::all());

			if ($status === 0) {
				App::abort(422);
			}

			// return Response::json(array('code' => 200), 200);
			return Response::json('L\'élément a été correctement sauvegardé!', 200);
		}
	}

	public function setHeadline()
	{

		if (Request::wantsJson()) {

			$status = $this->model->setHeadline(Request::all());

			if ($status === 0) {
				App::abort(422);
			}

			return Response::json(array('code' => 200), 200);
		}
	}

	public function export($method, $id = null, $relation = null)
	{

		if (method_exists($this->model, $method)) {
			$this->model->$method($relation, $id);
			return;
		}
		App::abort(422);
	}

	public function upload()
	{

		if (Request::wantsJson() && Request::hasFile('file')) {
			$ret = $this->model->saveMedia(Request::file('file'), Request::get('property'), 'single');
			return Response::json(['status' => 200, 'data' => $ret]);
		}

		App::abort(422);
	}

	public function setGridState()
	{
		$gridState = [
			'model' => Request::get('model'),
			'data'  => [
				'sort'      => null,
				'sortIndex' => Request::get('sortIndex'),
				'order'     => Request::get('order'),
				'page'      => Request::get('page'),
				'search'    => Request::get('search'),
				'length'    => Request::get('length'),
			]
		];

		Session::put('grid-state', $gridState);

		return $gridState;
	}

	public function resetGridState()
	{
		Session::pull('grid-state');
	}
}

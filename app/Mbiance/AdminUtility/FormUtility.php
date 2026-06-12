<?php

namespace Mbiance\AdminUtility;

use App\Models\Core\Category;
use App\Models\Core\CategoryGroup;
use App\Models\Core\Forms\FormAnswer;
use App\Models\Core\Model;
use App\Models\Core\Page;
use App\Models\Core\User;
use Arr;
use App\Models\Core\Translatable;
use Auth;
use Form;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\HtmlString;
use Lang;
use Mbiance\AdminUtility\Inputs\DateInput;
use Mbiance\AdminUtility\Inputs\EmailInput;
use Mbiance\AdminUtility\Inputs\FileInput;
use Mbiance\AdminUtility\Inputs\FileSelectorInput;
use Mbiance\AdminUtility\Inputs\MoneyInput;
use Mbiance\AdminUtility\Inputs\NumberInput;
use Mbiance\AdminUtility\Inputs\PasswordInput;
use Mbiance\AdminUtility\Inputs\PercentInput;
use Mbiance\AdminUtility\Inputs\SelectInput;
use Mbiance\AdminUtility\Inputs\SwitchInput;
use Mbiance\AdminUtility\Inputs\TextareaInput;
use Mbiance\AdminUtility\Inputs\TextInput;
use Mbiance\AdminUtility\Inputs\TimeInput;
use Mbiance\AdminUtility\Inputs\UrlInput;
use Request;
use Route;
use Session;
use Str;
use View;

/**
 * Class FormUtility
 *
 * @property Model|Translatable $model
 * @property ModelUtility $modelutility
 * @property PageUtility $pageutility
 * @package Mbiance\AdminUtility
 */
class FormUtility
{

	public $model;

	public $parentKey;

	public $model_name;

	public $collection_name;

	/**
	 * @var \ModelUtility
	 */
	public $modelutility;

	public $pageutility;

	public $form;

	/**
	 * FormUtility constructor.
	 *
	 * @param $form
	 * @param $modelutility \ModelUtility
	 * @param $pageutility \PageUtility
	 */
	public function __construct($form, $modelutility, $pageutility)
	{
		$this->form = $form;
		$this->modelutility = $modelutility;
		$this->pageutility = $pageutility;
	}

	/**
	 * @return string|void
	 */
	public function validationSummary()
	{

		$errors = Session::get('errors');

		if ($errors === null) {
			return;
		}

		$sb = $str = '';

		foreach ($errors->toArray() as $key => $value) {
			$str .= '            <li>' . $value[0] . "</li>\n";
			// $str .= "            <li><a href=\"#".$key."\" onclick=\"setfocus('".$key."');\" onkeypress=\"setfocus('".$key."');\">".$value[0]."</a></li>\n";
		}

		if (!empty($str)) {
			$sb .= '    <div style="padding: 0 15px"><div id="validationSummary" class="validationSummary">';
			$sb .= '        <p>' . __('admin.validation') . '</p>';
			$sb .= '        <ul>';
			$sb .= '            ' . $str;
			$sb .= '        </ul>';
			$sb .= '    </div></div><br>';
		}

		return $sb;
	}

	public function identifiant()
	{
		return ($this->model) ? $this->model->getLabel() : '';
	}

	public function notice(): string
	{
		return '<p class="notice">' . __('admin.notice') . '</p>';
	}

	public function getCollectionForSubgrid()
	{
		$child_name = $this->modelutility->getChildrenCollectionNameFromRoute();

		/** @var HasMany $relation */
		$relation = $this->model->$child_name();

		/** @var Model $relatedModel */
		$relatedModel = $relation->getRelated();

		if ($relatedModel->order_default === 'position') {
			$relation->orderBy('position');
		}

		if (method_exists($relatedModel, $methodName = 'filterGetRange')) {
			$relatedModel->$methodName($relation);
		}

		/** @var Collection $children */
		$children = mapChildren($relation->get());

		return json_encode($children, JSON_THROW_ON_ERROR | JSON_HEX_APOS);
	}

	public function open(array $options = array()): HtmlString
	{
		if ($relationObj = Session::get('admin.relationObj')) {
			switch (true) {

				case $relationObj instanceof HasMany:
				case $relationObj instanceof MorphMany:
				case $relationObj instanceof BelongsToMany:
					$childID = Route::input('childId');

					if ($this->modelutility->getChildrenCollectionNameFromRoute() === 'blocs') {
						$model = Request::get('bloc_type');
						$model = Arr::get(config('classmap'), $model);
						$model = new $model();
					} else {
						$model = $this->modelutility->getChildModel();
					}

					if ($childID && $childID !== 'create') {
						/** @var Model $model */
						$model = $model::find($childID);
					}

					$this->setModel($model);
					break;

				case $relationObj instanceof HasOne:
				case $relationObj instanceof MorphOne:
					$model = $relationObj->firstOrNew();

					$this->setModel($model);

					break;


				default:
			}

		}

		$primaryKeyName = $this->model->getKeyName();

		if ($this->model->$primaryKeyName) {
			$options['method'] = 'PUT';

			if (!isset($options['action'])) {
				$options['url'] = adminRouteName("admin.$this->collection_name.update",
					[$this->model->$primaryKeyName]);
			}

		} elseif (!isset($options['action'])) {

			$options['url'] = adminRouteName("admin.$this->collection_name.store");
		}

		$options['files'] = true;
		$options['class'] = 'form-horizontal';
		$options['novalidate'] = 'novalidate';
		$options['recaptcha'] = false;

		return Form::model($this->model, $options);
	}

	/**
	 * @param Model $model
	 * @param $relation
	 * @param $childID
	 * @return void
	 */
	public function setModel($model, $relation = null, $childID = null): void
	{
		$this->model = $model;

		$this->model_name = get_class($model);

		$this->collection_name = $model->collection_name;

		switch ($this->model_name) {
			case Category::class:
				$this->categoryFormSetup();
				break;
			case Page::class:
				$this->pageFormSetup();
				break;
		}
	}

	public function categoryFormSetup(): void
	{

		$categorygroup = CategoryGroup::find((int)Request::segment(3))->identifier;

		// Recuperation des champs ajoutés afin de les retirer a tout les groupes sauf ceux choisis
		$standardgroup = [
			'tvq',
			'tps'
		];

		// Modification des champs pour les categories, creation du model de base
		if ($categorygroup !== 'provinces') {
			$this->model['fillable'] = array_diff($this->model['fillable'], $standardgroup);
		}
	}

	public function pageFormSetup(): void
	{

		/** @var Page $model */
		$model = $this->model;

		if ($model->integrated) {

			// Récupère les paramètres de la page intégrée
			$config = config('routes.front-end');
			$params = Arr::get($config, $model->label, []);


			// Paramètres ne pouvant être écrasés
			$except = [
				'@ Paramètres avancés',
				'custom_code',
				'custom_url',
				'restricted',
			];

			// Titre?
			if (!Arr::get($params, 'admin.title', true)) {
				$except[] = 'title';
			}

			// Metas?
			if (!Arr::get($params, 'admin.metas', true)) {
				array_push($except, 'meta_title', 'meta_description');
			}

			// Personnalisation?
			if (!Arr::get($params, 'admin.customs', true)) {
				array_push($except, '@ Personnalisation', 'slideshow_id', 'banner_image', 'banner_height',
					'page_top_spacing', 'footer_top_spacing');
			}

			// Colonne de droite?
			if (!Arr::get($params, 'admin.right_column', true)) {
				array_push($except, '@ Colonne de droite', 'has_right_column', 'pub_group_id');
			}

			$fillable = [];
			foreach ($model['fillable'] as $field) {
				if (!in_array($field, $except, true)) {
					$fillable[] = $field;
				}
			}

			$fillable[0] = '@ Paramètres de page intégrée';
			$model['fillable'] = $fillable;

			$customFields = $model['customFields'];

			$customFields['label'] = ['widget' => 'readonly'];
			$customFields['active'] = ['widget' => 'hidden'];
			$customFields['publication_date'] = ['widget' => 'skip'];

			$model['customFields'] = $customFields;
		}
	}

	public function getTypeBloc()
	{

		$item = $this->getProperty('bloc_type');

		if ($item) {

			return $item;
		}

		if (Request::has('bloc_type')) {

			return Request::get('bloc_type');
		}
	}

	public function getProperty($name)
	{

		return $this->model->$name;
	}

	public function relation($name): HtmlString
	{

		return Form::hidden($name, Request::segment(3));
	}

	public function getPageableType(): string
	{
		return $this->modelutility->getClassNameFromRoute();
	}

	public function getBlocableType()
	{
		$type = Request::get('bloc_type');
		return Arr::get(config('classmap'), $type);
	}

	public function time($name, $label = null, $value = null, $options = array())
	{

		return $this->input('time', $name, $label, $value, $options);
	}

	public function input(
		$type,
		$name,
		$label = null,
		$value = null,
		$options = array(),
		$list = array(),
		$locale = null
	)
	{
		$str = '';
		$errors = Session::get('errors');
		$isRequired = $this->modelutility->isFieldRequired($name, $this->model['rules']); //vérifier si requis
		$isRestricted = false;

		if ($this->model['restricted'] && isset($this->model['restricted'][$name])) {
			/** @var User $user */
			$user = Auth::guard('users')->user();
			if (in_array($user->name, $this->model['restricted'][$name], true)) {
				$isRestricted = true;
			}
		}

		if (($errors !== null) && $errors->has($name)) {
			$isError = true;
		}

		if (is_array($label)) {
			$options = $label;
			$label = null;
		}

		if (is_array($value)) {
			$options = $value;
			$value = null;
		}

		if (!$label) {
			$label = $this->getLabel($name);
		}

		if (isset($options['class'])) {
			$options['class'] .= ' form-control';
		} else {
			$options['class'] = 'form-control';
		}

		if ($type === 'select') {
			$options['class'] .= ' ui dropdown fluid search';
		}

		if ($locale) {
			$id = $locale . '_' . $name;
			$fieldname = $locale . '[' . $name . ']';
		} else {
			$id = $fieldname = $name;
		}

		$options['id'] = $id;
		$options['placeholder'] = $this->model->getFieldPlaceholder($fieldname);
		//angular
		// $options['data-ng-model'] = "vm.".$fieldname;
		// if ($isRequired) {
		// 	$options['required'] = "required";
		// }
		//$str .= "<div id=\"".$fieldname."_group\" data-ng-form=\"mainForm.".$fieldname."\" data-ng-class=\"{'has-error': mainForm.".$fieldname.".\$dirty && mainForm.".$fieldname.".\$invalid }\" class=\"form-group " . ( $isRequired ? ' has-feedback' : '') . ( $errors && $errors->has($fieldname) ? ' has-error' : '') . "\">\n";

		$toggle = Arr::get($this->model['toggleFields'], $name);

		$str .= '<div id="' . $id . '_group" ' . ($toggle ? 'data-toggle="' . $toggle . '"' : '') . ' class="form-group ' . ($isRequired ? ' has-feedback' : '') . ($errors && $errors->has($id) ? ' has-error' : '') . "\">\n";

		$str .= '<label for="' . $id . '" class="col-md-3 control-label">' . ($isRequired ? '<i class="fa fa-asterisk"></i> ' : '') . ($isRestricted ? '<i class="fa fa-lock"></i> ' : '') . $label . ' <span class="sr-only">' . ($isRequired ? __('form.required') : __('form.optional')) . "</span>:</label>\n";

		$str .= "<div class=\"col-md-9\">\n";

		$right = '15';

		switch ($type) {
			case 'time':
				$str .= TimeInput::generate($this->form, $fieldname, $value, $options);
				break;

			case 'text':
				$str .= TextInput::generate($this->form, $fieldname, $value, $options);
				break;

			case 'email':
				$str .= EmailInput::generate($this->form, $fieldname, $value, $options);
				break;

			case 'password':
				$str .= PasswordInput::generate($this->form, $fieldname, $options);
				break;

			case 'url':
				$str .= UrlInput::generate($this->form, $fieldname, $value, $options);
				break;

			case 'select':
				$list = [null => 'Sélectionner un élément'] + $list;
				$str .= SelectInput::generate($this->form, $fieldname, $list, $value, $options);
				$right = '30';
				break;

			case 'morph':
				$str .= $this->getMorphList($fieldname, $list, $value, $options);
				$right = '30';
				break;

			case 'textarea':
				$str .= TextareaInput::generate($this->form, $fieldname, $value, $options);
				break;

			case 'wysiwyg':
				$obj = $this->model;
				$field = $fieldname;
				return View::make(
					$this->pageutility->getWidgetPath('wysiwyg'),
					compact('obj', 'field', 'options', 'label', 'isRequired')
				);
				break;

			case 'file_selector':
				$str .= FileSelectorInput::generate($this->form, $fieldname, $value, $options, false, $id);
				break;

			case 'money':
				$str .= MoneyInput::generate($this->form, $fieldname, $value, $options);
				break;
			case 'percent':
				$str .= PercentInput::generate($this->form, $fieldname, $value, $options);
				break;

			case 'date':

				$str .= DateInput::generate(
					$this->form,
					$fieldname,
					$value,
					$options,
					$this->model->fieldIsRequired($fieldname)
				);

				if (
					Str::contains($fieldname, [
						'created_at',
						'updated_at',
						'datetime'
					])
				) { //récupération de l'heure
					$str .= TimeInput::generate($this->form, $fieldname . '-datetime', $value, $options);
				}
				break;

			case 'number':
				$str .= NumberInput::generate($this->form, $fieldname, $value, $options);
				break;

			case 'file':
				$model = $locale ? $this->model->translate($locale) : $this->model;
				$image = $model->$name ?? '';
				$title = $model->title ?? '';
				$str .= FileInput::generate(
					$this->form,
					$fieldname,
					$image,
					$title,
					$locale,
					$isRequired
				);
				break;

			case 'switch':
				$str .= SwitchInput::generate($this->form, $fieldname, $value);
				break;
		}

		if ($isRequired) {

			//ANGULAR
			//$str .= "       <span style=\"position:absolute;right:".$right."px;\" class=\"fa fa-check form-control-feedback\"></span>\n";

			//$str .= "       <span style=\"position:absolute;right:".$right."px;padding:10px;\" class=\"fa fa-asterisk form-control-feedback\"></span>\n";
			$str .= '       <span style="position:absolute;right:' . $right . "px;padding:10px;display:none;\" class=\"fa fa-remove form-control-feedback\"></span>\n";
		}

		//ACCESSIBILITY
		if ($this->getFieldInstruction($fieldname) !== '') {
			$str .= $this->getFieldInstruction($fieldname);
		}

		$i = Arr::get($this->model['extraInfos'], $fieldname);
		if ($i !== null) {
			$str .= $i . "\n";
		}

		$str .= "    </div>\n";
		$str .= "</div>\n";
		return $str;
	}

	public function getLabel($name)
	{

		$mainField = $name;
		$lang = '';
		$label = null;

		$i = Arr::get($this->model['niceNames'], $mainField);

		if ($i !== null) {

			$label = $i;

		} elseif (empty($lang) && Str::contains($name, 'url_')) {

			$element = str_replace('url_', 'Url ', $name);

			$label = ucwords($element);

		} else {

			$label = __("properties.$mainField");
		}

		return $label;
	}

	public function getFieldInstruction($name): string
	{

		$name = $this->sanitizeFieldName($name);
		$r = Arr::get($this->model['rules'], $name);
		foreach (explode('|', $r) as $key => $rule) {

			if (Str::contains($rule, 'min') && Str::contains($rule, 'numeric')) {

				$f = Lang::get('validation.min');
				$c = explode(':attribute ', $f['string'])[1];
				$p = explode(':', $rule)[1];
				$s = str_replace(':min', $p, $c);
				$msg = ucfirst($s);
				break; //juste un message par champ

			}

			if (Str::contains($rule, 'max')) {

				$f = Lang::get('validation.max');
				$c = explode(':attribute ', $f['string'])[1];
				$p = explode(':', $rule)[1];
				$s = str_replace(':max', $p, $c);
				$msg = ucfirst($s);
				break; //juste un message par champ

			}

			if (Str::contains($rule, 'mimes')) {

				$f = Lang::get('validation.mimes');
				$c = explode(':attribute ', $f)[1] ?? null;
				$p = explode(':', $rule)[1];
				$s = str_replace(':values', $p, $c);
				$msg = ucfirst($s);
				break; //juste un message par champ

			}
		}

		if (!empty($msg)) {
			return '	 <span class="help-block col-sm-8">' . $msg . "</span>\n";
		}

		return '';
	}

	private function sanitizeFieldName($name)
	{
		return preg_replace('/(\w+\[)(\w+-?\w*)(\])/', '${2}', $name);
	}

	public function text($name, $label = null, $value = null, $options = array())
	{

		return $this->input('text', $name, $label, $value, $options);
	}

	public function textarea($name, $label = null, $value = null, $options = array())
	{

		return $this->input('textarea', $name, $label, $value, $options);
	}

	public function file($name, $label = null, $value = null, $options = array())
	{

		return $this->input('file', $name, $label, $value, $options);
	}

	public function file_selector($name, $label = null, $value = null, $options = array())
	{

		return $this->input('file_selector', $name, $label, $value, $options);
	}

	public function url($name, $label = null, $value = null, $options = array())
	{

		return $this->input('url', $name, $label, $value, $options);
	}

	public function email($name, $label = null, $value = null, $options = array())
	{

		return $this->input('email', $name, $label, $value, $options);
	}

	public function password($name, $label = null, $value = null, $options = array())
	{

		return $this->input('password', $name, $label, $value, $options);
	}

	public function select($name, $label = null, $value = null, $list = array(), $options = array())
	{

		if ((is_array($label)) && (is_array($value))) {

			$list = $label;
			$options = $value;
			$label = null;
			$value = null;
		} else {

			if (is_array($label)) {
				$list = $label;
				$label = null;
			}

			if (is_array($value)) {
				$list = $value;
				$value = null;
			}
		}


		if (!$list) {
			$list = Arr::get($this->model['enum'], $name);
			if (!$list) {
				$list = array();
			}
		}

		return $this->input('select', $name, $label, $value, $options, $list);
	}

	public function date($name, $label = null, $value = null)
	{

		return $this->input('date', $name, $label, $value);
	}

	public function checkboxSwitch($name, $label = null, $value = null)
	{

		return $this->input('switch', $name, $label, $value);
	}

	public function wysiwyg($name, $label = null, $value = null, $options = array())
	{

		if (is_array($label)) {
			$options = $label;
			$label = null;
		}

		return $this->input('wysiwyg', $name, $label, $value, $options);
	}

	public function widget($field): \Illuminate\Contracts\View\View
	{

		$mainField = $field;
		$customFields = $this->model['customFields'];

		if (Str::endsWith($field, '_fr')) {
			$mainField = str_replace('_fr', '', $field);
		} elseif (Str::endsWith($field, '_en')) {
			$mainField = str_replace('_en', '', $field);
		}

		$widget_name = $customFields[$mainField]['widget'];
		if (isset($customFields[$mainField]['options'])) {
			$options = $customFields[$mainField]['options'];
		}

		$options['id'] = $field; //ajouter cet élément aux options pour traitement sur widget
		$label = $this->getLabel($field);

		$obj = $this->model;
		$isRequired = $this->modelutility->isFieldRequired($field, $this->model['rules']);
		return View::make(
			$this->pageutility->getWidgetPath($widget_name),
			compact('obj', 'field', 'options', 'label', 'isRequired')
		);
	}

	public function saveBtn($name = null): string
	{

		if (!$name) {
			$name = __('admin.save');
		}
		$str = '</div>';
		$str .= '<div class="panel-footer">';

		if (!$this->model instanceof FormAnswer) {
			if ($this->model['canRecalculate']) {
				$recalculateRoute = adminRouteName("admin.{$this->collection_name}.recalculate", [$this->model->id]);

				$str .= '    <div class="col-md-offset-3"><button type="submit" class="btn btn-primary">' . $name . '</button>';
				$str .= "&nbsp; <button type='submit' class='btn btn-success' onclick='form.action='{$recalculateRoute}';'>Recalculer</button></div>";
			} else {
				$str .= '    <div class="col-md-offset-3"><button type="submit" class="btn btn-primary">' . $name . '</button></div>';
			}
		}

		$str .= '</div>';

		return $str;
	}

	public function close(): string
	{

		return Form::close();
	}

	public function generateForm($fields = array()): string
	{

		$readOnlyFields = $this->model['readOnlyFields'];
		$customFields = $this->model['customFields'];
		$isEditMode = !$this->pageutility->isCreate();
		$sb = '';

		if (count($fields) === 0) {
			$fields = $this->model->getFormFields();
		}

		foreach ($fields as $field) {
			$list = '';
			$options = array();

			$type = $this->model->getFieldType($field); //$type dans la base de données

			if ($field === 'password_confirmation') {
				$type = 'varchar(60)';
			} //spécifier car pas dans la base de données

			$mainField = $field;

			if ($field[0] === '@') {
				$sb .= '<h2>' . substr($field, 1) . '</h2>';
				continue;
			}

			if ($field === 'identifier' && $this->model instanceof CategoryGroup && !Auth::guard('users')->user()->is_mbiance) {
				$readOnlyFields[] = $field;
			}

			if (in_array($mainField, $readOnlyFields, true)) { //voir si readOnly - mode edit
				$options['disabled'] = 'disabled';
			}

			if (array_key_exists($mainField, $customFields)) { //voir custom elements

				$widget_name = $customFields[$mainField]['widget'];
				if (isset($customFields[$mainField]['options'])) {
					$options = $customFields[$mainField]['options'];
				}

				if ($widget_name !== 'skip') {

					$value = $this->model->$field;
					$isRequired = $this->modelutility->isFieldRequired(
						$field,
						$this->model['rules']
					); //vérifier si requis

					$toggle = Arr::get($this->model['toggleFields'], $field);

					if (
						isset($this->model['translatedAttributes'])
						&&
						in_array($field, $this->model['translatedAttributes'], true)
					) {
						$locales = getLocales();
						$count = count($locales);
						foreach ($locales as $locale) {
							$value = $this->model->translate($locale)->$field ?? '';
							$options['id'] = $field . '_' . $locale;
							$sb .= View::make($this->pageutility->getWidgetPath($widget_name), [
								'value'      => $value ?? '',
								'obj'        => $this->model,
								'field'      => $locale . '[' . $field . ']',
								'options'    => $options,
								'label'      => Arr::get(
										$this->model['niceNames'],
										$field,
										__('properties.' . $field)
									) . ($count > 1 ? ' (' . $locale . ')' : ''),
								'isRequired' => $isRequired,
								'toggle'     => $toggle
							]);
						}
					} else {
						$obj = $this->model;
						$options['id'] = $field;
						$label = $this->getLabel($field);
						$sb .= View::make(
							$this->pageutility->getWidgetPath($widget_name),
							compact('obj', 'value', 'field', 'options', 'label', 'isRequired', 'toggle')
						);
					}
				}
				continue;
			}

			if (Str::endsWith($field, '_type')) {
				$possibleRelationName = Str::camel(str_replace('_type', '', $field));
				if (
					method_exists($this->model, $possibleRelationName)
					&&
					$this->model->$possibleRelationName() instanceof MorphTo
				) {
					continue;
				}
			}

			if (array_key_exists($mainField, $this->model['enum'])) {
				$typeInput = 'select';
				$list = Arr::get($this->model['enum'], $mainField);

			} elseif (Str::contains($type, 'bool')) {
				$typeInput = 'switch';

			} elseif ($field === 'tps' || $field === 'tvq' || Str::endsWith($field, '_percent')) {
				$typeInput = 'percent';

			} elseif ($field === 'price' || Str::endsWith($field, '_price')) {
				$typeInput = 'money';

			} elseif (Str::contains($type, 'tinyint')) {
				$typeInput = 'switch';

			} elseif (Str::contains($type, 'int')) {
				if (Str::endsWith($field, '_id')) {
					$parent_class_key = Str::snake(Str::singular($this->modelutility->getCollectionNameFromRoute())) . '_id';

					// Check if is onglet and is child
					$childId = Route::input('childId');
					if (
						$field === $parent_class_key
						&&
						$childId
					) { //si c'est le cas

						$id_relation = Request::segment(3);
						$sb .= Form::hidden($field, $id_relation);
						continue;
					}

					$relationAttribute = str_replace('_id', '', $field);
					$relationName = Str::camel($relationAttribute);

					if (!method_exists($this->model, $relationName)) {
						$sb .= "<div class='form-group has-error'><div class='col-md-9 col-md-offset-3'><div class='form-control'>Relation manquante : $relationName</div></div></div>";
						continue;
					}

					/** @var BelongsTo $relation */
					$relation = $this->model->$relationName();

					if ($relation instanceof MorphTo) {
						if (($parentRelation = Session::get('admin.relationObj')) && $parentRelation instanceof MorphMany && $parentRelation->getForeignKeyName() === $field) {
							$parentClass = $this->modelutility->getClassNameFromRoute();
							$typeField = $parentRelation->getMorphType();
							$sb .= Form::hidden($typeField, $parentClass);


							$id_relation = Request::segment(3);
							$sb .= Form::hidden($field, $id_relation);
							continue;
						}

						$list = Arr::get($this->model['morphClasses'], $relationName);
						$typeField = $relationAttribute . '_type';

						if ($list === null) {
							$sb .= "<div class='form-group has-error'><div class='col-md-9 col-md-offset-3'><div class='form-control'>Morph $relationName non spécifié dans \$morphClasses</div></div></div>";
							continue;
						}

						$sb .= $this->input('select', $typeField, null, $this->model->$typeField,
							[
								'class'    => 'morph morph-type',
								'data-key' => $field
							], $list);

						$sb .= $this->input('morph', $field, null, $this->model->$field,
							[], $list);

						continue;
					}

					/** @var Model $element */
					$element = $relation->getRelated();

					$typeInput = 'select';
					if ($element) {
						$list = $element::getList();
					} else {
						$typeInput = '';
					}

				} elseif (($type === 'money') || $this->fieldHasKeyword($field, '_price')) {
					$typeInput = 'money';

				} else {
					$typeInput = 'number';
				}

			} elseif (Str::contains($type, 'text')) {
				$typeInput = 'textarea';

			} elseif (Str::contains($type, 'varchar')) {

				if (Str::contains($field, 'filename')) {
					$typeInput = 'file_selector';

				} elseif (
					Str::endsWith($field, 'document')
					||
					Str::endsWith($field, 'image')
					||
					Str::endsWith($field, 'file')
					||
					Str::endsWith($field, 'photo')) {
					$typeInput = 'file';

				} elseif (Str::endsWith($field, 'url')) {
					$typeInput = 'url';

				} elseif (Str::endsWith($field, 'email')) {
					$typeInput = 'email';

				} elseif (Str::endsWith($field, 'password') || Str::endsWith($field, 'password_confirmation')) {
					$typeInput = 'password';

				} elseif (Str::endsWith($field, [
					'description',
					'content'
				])) {
					$typeInput = 'textarea';

				} else {

					$typeInput = 'text';

				}

			} elseif (in_array($field, [
					'created_at',
					'updated_at'
				])
				||
				in_array($type,
					[
						'datetime',
						'date',
						'timestamp'
					])) //DATE OR DATETIME
			{
				$typeInput = 'date';

			} elseif ($type === 'time') {
				$typeInput = $type;

			} else {
				$typeInput = 'number';
			}

			if (
				in_array(Translatable::class, class_uses($this->model), true)
				&&
				isset($this->model['translatedAttributes'])
				&&
				in_array($field, $this->model['translatedAttributes'], true)
			) {
				$locales = getLocales();
				$count = count($locales);
				foreach ($locales as $locale) {
					$value = $this->model->translate($locale)->$field ?? '';
					$sb .= $this->input(
						$typeInput,
						$field,
						Arr::get(
							$this->model['niceNames'],
							$field,
							__('properties.' . $field)
						) . ($count > 1 ? ' (' . $locale . ')' : ''),
						$value ?? '',
						$options,
						$list,
						$locale
					);
				}
			} else {
				$sb .= $this->input($typeInput, $field, null, $this->model->$field, $options, $list);
				/*	if ($list) {
					$sb .= $this->input($typeInput, $field, null, null, null, $list);
				} else {
					$sb .= $this->input($typeInput, $field, $options);
				}*/
			}
		}
		return $sb;
	}

	private function fieldHasKeyword($field, $keyword)
	{
		if (Str::contains($keyword, '_')) {
			$keyword = str_replace('_', '', $keyword);
			return Str::contains($field, $keyword . '_') || Str::contains($field, '_' . $keyword);
		}

		return Str::contains($field, $keyword);
	}

	public function getMorphList($name, $list, $value, $options)
	{
		$selectOptions = '';

		/** @var Model $class */
		foreach ($list as $class => $classNiceName) {
			foreach ($class::getList() as $subValue => $subItem) {
				$selectOptions .= "<div class='item disabled' data-class='$class' data-value='$subValue'>$subItem</div>";
			}
		}

		return "<div class='form-control ui dropdown fluid search selection' data-morph='$name'>"
			. Form::hidden($name, $value)
			. '<i class="dropdown icon"></i>'
			. '<div class="default text">Sélectionner un élément</div>'
			. '<div class="menu">'
			. $selectOptions
			. '</div>'
			. "</div>";
	}
}

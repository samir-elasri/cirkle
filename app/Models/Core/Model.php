<?php

/**
 * @noinspection PhpPossiblePolymorphicInvocationInspection
 * @noinspection PropertyInitializationFlawsInspection
 * @noinspection UnusedFunctionResultInspection
 * @noinspection PhpUnused
 * @noinspection SqlResolve
 * @noinspection SqlNoDataSourceInspection
 */

namespace App\Models\Core;

use App;
use App\Scopes\AdminScope;
use Arr;
use Auth;
use Cache;
use Carbon\Carbon;
use DB;
use Eloquent;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection as SupportCollection;
use Lang;
use Mbiance\MediaUtility\MediaTrait;
use ModelUtility;
use ReflectionClass;
use Request;
use Response;
use Route;
use RoutingUtility;
use Schema;
use Session;
use Str;
use StringUtility;
use Validator;

/**
 * App\Models\Core\Model
 *
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @method static Builder|Model active()
 * @method static Builder|Model newModelQuery()
 * @method static Builder|Model newQuery()
 * @method static Builder|Model query()
 * @mixin Eloquent
 */
class Model extends Eloquent
{
	use MediaTrait, BigDataTrait;

	public static array $associateCategoriesTableExists = [];

	public bool $isCache = true;

	public bool $isAjaxEnabled = false;

	public bool $backAfterChange = true;

	public string $order_default = 'id';

	public string $order_direction = 'ASC';

	public $errors;

	public bool $canRecalculate = false;

	public string $singular = 'un élément';

	public array $positionParentFields = [];

	protected array $rules = [];

	protected $touches = [];

	protected array $readOnlyFields = [];

	protected array $niceNames = [];

	protected array $customFields = [];

	protected array $gridFields = [];

	protected array $restricted = [];

	protected array $grid = [];

	protected array $medias = [];

	protected array $enum = [];

	protected $appends = [];

	protected array $fieldTypes = [];

	protected array $toggleFields = [];

	protected array $morphClasses = [];

	protected array|false|null $describeResults = null;

	protected bool $bigData = false;

	/*===========================
	=  IDENTIFIER
	===========================*/

	public static function translateRouteUri($locale): array
	{

		/** @var Model|Translatable $entity */
		$entity = static::find(Route::input('id'));

		return ['slug' => $entity ? slug($entity->translate($locale)->title) : ''];
	}

	/**
	 * Détermine si une proprété existe dans le model : verification dans "fillable" et "appends"
	 *
	 * @param string $field nom de la propriété
	 * @return boolean
	 */
	public function isPropertyExists($field): bool
	{
		return in_array($field, $this->fillable, true) || in_array($field, $this->appends, true);
	}

	/**
	 * Sert à construire une liste pour les menus déroulants d'un model dans le CMS
	 * Par défaut va vérifier si une propriété label existe ... si non title
	 *
	 * @return array
	 */
	public static function getList(): array
	{
		$model = new static;
		$identifier = $model->getIdentifier();
		$items = $model->isPropertyExists('active') ? static::where('active', 1)->get() : static::all();
		$select = [];
		foreach ($items as $item) {
			$select[$item->id] = $item->$identifier;
		}
		return $select;
	}

	/**
	 * @return string
	 */
	public function getIdentifier(): string
	{
		if ($this->isPropertyExists('name')) {
			return 'name';
		}

		if ($this->isPropertyExists('label')) {
			return 'label';
		}

		return 'title';
	}

	public static function lists($name, $id, $collection): array
	{
		$select = [];
		foreach ($collection ?? static::all() as $item) {
			$select[$item->$id] = $item->$name;
		}

		return $select;
	}

	public static function getGridList(): string
	{
		$children = mapChildren(static::orderBy('position')->get());

		return json_encode($children, JSON_HEX_APOS);
	}

	public static function getLastCreatedId()
	{
		$model = new static();
		return DB::table($model->getTable())->orderBy('id', 'DESC')->first()->id;
	}

	/**
	 * @return Builder
	 */
	public static function getNew(): Builder
	{
		/** @var User $user */
		$user = Auth::guard('users')->user();
		$previous_login = $user->previous_login;
		return static::where('created_at', '>', $previous_login);
	}

	/**
	 * Retourne une sélection d'éléments actifs de la collection
	 * [Eloquent\Collection]  même si un seul résultat
	 *
	 * @var int $nb - nombre d'éléments désirés
	 * @return Collection
	 */
	public static function getRandomSelection($nb = 1): Collection
	{
		$collections = static::get();
		return (count($collections) <= $nb) ? $collections : $collections->random($nb);
	}

	/**
	 * [getEnum description]
	 *
	 * @param string $type [type de l'enum - par défaut 'bloc_type']
	 * @return SupportCollection [Illuminate\Support\Collection] [retourne key - value de l'enum désiré]
	 */
	public static function getEnum($type = 'blocable_type'): SupportCollection
	{

		$c = new static();
		if (isset($c->enum[$type])) {
			return new SupportCollection($c->enum[$type]);
		}
		return new SupportCollection();
	}

	/**
	 * Traitement simple de la recherche pour un Model
	 * Sur la base uniquement de la présence des mots dans un model ou ses enfants identifiés
	 * Les propriétés interrogés sont exprimés dans une propriété static $search (array des champs)
	 * Tous les mots (de 3 lettres et plus) de la requête doivent se retrouver pour que le résultat soit compter
	 *
	 * @param  [string] mot à interroger
	 * @return Collection [type] retourne la collection
	 */
	public static function search($q): Collection
	{
		$words = preg_split('/\s+/', urldecode($q), -1, PREG_SPLIT_NO_EMPTY);
		$count = count($words);
		$result = new Collection();
		$list = static::getListForSearch();

		foreach ($list as $item) {
			/** @var Model $item */
			$find = $item->_search($words);
			if ($count === count($find)) {
				$result->add($item->search_result);
			}
		}

		return $result;
	}

	/*===========================
	=  TRAITEMENTS
	===========================*/

	protected function _search($words, &$result = [])
	{

		$fields = $this->searchFields;
		$count = count($words);

		if (count($fields)) {

			foreach ($fields as $field) {

				$value = $this->$field;

				if (is_string($value)) {

					$value = StringUtility::replaceAccent($value);

					// Rajouter \b au / et | si besion que le mot commence par $word
					if (preg_match_all(
						'/' . StringUtility::replaceAccent(implode('|', $words)) . '/i',
						$value,
						$matches
					)) {

						foreach ($matches[0] as $match) {
							$result[] = mb_strtolower($match);
						}

						$result = array_unique($result);
					}
				} elseif (is_collection($value)) {

					foreach ($value as $child) {

						/** @var Model $child */
						if (is_model($child)) {
							//							$result = array_unique(array_merge($result, $child->_search($words)));
							$child->_search($words, $result);

							if ($count === count($result)) {
								break;
							}
						}
					}

				} elseif (is_model($value)) {

					//					$result = array_unique(array_merge($result, $value->_search($words)));
					/** @var Model $value */
					$value->_search($words, $result);
				}

				if ($count === count($result)) {
					break;
				}
			}
		}

		$result = array_unique($result);
		return $result;
	}

	/**
	 * @param        $id
	 * @param        $name
	 * @param null $placeholder
	 * @return array
	 */
	public static function getSelect($id, $name, $placeholder = null): array
	{
		$model = new static();
		$select = [];
		if ($placeholder) {
			$select[0] = $placeholder;
		}
		foreach ($model::where('active', true)->get() as $item) {
			$select[$item->$id] = $item->$name;
		}
		return $select;
	}

	/**
	 * Evénements de modèle
	 *
	 * Chaque fois qu'un nouvel item est sauvegardé pour la première fois, les événements creating et created sont lancés.
	 * Si un item n'est pas nouveau et que la méthode save est appelée, les événements updating / updated sont lancés.
	 * Dans les deux cas, les événements saving / saved sont lancés.
	 *
	 * à noter qu'on peut également appliquer des retours
	 * Si false est retourné par la méthode creating, updating, ou saving, alors l'action est annulée :
	 */
	protected static function booted()
	{
		static::addGlobalScope(new AdminScope());

		/**
		 * saving - traitement avant la sauvegarde
		 */
		static::saving(static function ($model) {
			/** @var Model $model */
			$collection_name = $model->collection_name;

			// Si en création
			if (!$model->exists && ($model->isFieldExists('position')) && ($model->position === null)) { //si position existe mais pas dans la requête

				//vérifier si order pour table de relation - touches est utilisé pour cela ... car vocation compatible avec le besoin.

				if ($collection_name === 'menu_trees') {

					$parent_id = $model->parent_id;
					$group = $model->group;
					//$lang =  $model->lang;
					$o = DB::table($model->getTable())
							->where('parent_id', $parent_id)
							->where('group', $group)
							->max('position') + 1; //Where('lang', $lang)->

				} else {

					$q = DB::table($model->getTable());

					$positionParentFields = $model['positionParentFields'];
					if (count($positionParentFields) > 0) {

						foreach ($positionParentFields as $key) {
							if (empty($model->$key)) {
								$q->whereNull($key);
							} else {
								$q->where($key, $model->$key);
							}

						}
					}

					$o = $q->max('position') + 1;
				}

				$model->position = $o;
			}
		});

		/**
		 * saved - traitement après la sauvegarde
		 */
		static::saved(static function ($model) {

			/** @var Model $model */
			if ($model->isCache) {
				$model->deleteCache();
			}

			if (isset($model['resetCacheOnChange'])) { //delete parent cache
				$model->resetRelatedCache($model['resetCacheOnChange']);
			}

			if (method_exists($model, 'createCustomCache')) {
				$model->createCustomCache();
			}

			if (!empty($model['resetPages'])) {
				foreach ($model['resetPages'] as $cache => $infos) {
					$model->resetPageCache($cache, $infos);
				}
			}
		});

		/**
		 * deleting - traitement avant la suppression
		 */
		static::deleting(static function ($model) {

			/** @var Model $model */
			if ($model->isFieldExists('position')) {
				if ($model instanceof MenuTree) {
					$sql = $model->menutreeUpdatePositionSql();
					DB::statement($sql);
				} else {
					$model->updatePositions($model);
				}

			}
		});

		/**
		 * deleted - traitement après la suppression
		 */
		static::deleted(static function ($model) {

			/** @var Model $model */
			if ($model->isCache) {
				$model->deleteCache();
			}

			if (isset($model['resetCacheOnChange'])) { //delete parent cache
				$model->resetRelatedCache($model['resetCacheOnChange']);
			}

			if (method_exists($model, 'createCustomCache')) {
				$model->createCustomCache();
			}
		});
	}

	/**
	 * @return array|null structure de la base de données
	 */
	public function describe(): ?array
	{
		if (!isset($this->describeResults)) {
			$table = $this->getTable();
			$this->describeResults = DB::connection()
				->getPdo()
				->query("describe $table")
				->fetchAll();
		}

		return $this->describeResults;
	}

	/**
	 * Détermine si une proprété existe dans la base de données
	 *
	 * @param string $field nom de la propriété
	 * @return boolean
	 */
	public function isFieldExists($field): bool
	{
		return in_array($field, Arr::pluck($this->describe(), 'Field'), true);
	}

	public function deleteCache()
	{
		Cache::forget($this->getCacheKey());
	}

	public function resetRelatedCache(array $lists = array())
	{

		if (empty($lists)) {
			return;
		}

		foreach ($lists as $parent_class_name) {
			$m = new $parent_class_name();
			if ($m->isCache) {
				$m->deleteCache();
			}
		}
	}

	/**
	 * @param $blocType
	 * @param $infos
	 * @throws Exception
	 */
	public function resetPageCache($blocType, $infos)
	{
		/** @var Bloc $blocType */
		$bloc_ids = $blocType::where($infos['relation'], $this->{$infos['id']})->pluck('id')->toArray();

		$blocs = Bloc::where('blocable_type', $blocType)->whereIn('blocable_id', $bloc_ids)->get();
		foreach ($blocs as $bloc) {
			/** @var Model $className */
			$className = $bloc->pageable_type;
			$page = ($className)::find($bloc->pageable_id);

			if (Cache::has($page->getCacheKey())) {
				$page->deleteCache();
			}
		}
	}

	/**
	 * @return string
	 */
	public function menutreeUpdatePositionSql(): string
	{
		return "UPDATE menu_trees SET position = position - 1 WHERE position > {$this->position} AND `group` = '{$this->group}'"
			. ($this->parent_id ? " AND parent_id = {$this->parent_id}" : " AND parent_id is null")
			. ";";
	}

	/**
	 * @param self $model
	 */
	public function updatePositions($model)
	{
		$q = DB::table($model->getTable())
			->where('position', '>', $model->position);

		$positionParentFields = $model['positionParentFields'];
		if (count($positionParentFields) > 0) {

			foreach ($positionParentFields as $key) {
				if (empty($model->$key)) {
					$q->whereNull($key);
				} else {
					$q->where($key, $model->$key);
				}
			}
		}

		$q->decrement('position');
	}

	/**
	 * @return string
	 */
	public function getCacheKey(): string
	{
		return $this->getTable();
	}

	/**
	 * @return mixed
	 */
	public function getLabel()
	{
		return $this[$this->getIdentifier()];
	}

	/** @noinspection UnknownColumnInspection */
	public function getAssociateCategories($attribute, $idOnly = false)
	{
		$value = Arr::get($this->customFields, $attribute);
		$options = Arr::get($value, 'options');

		if (
			!empty($options['table'])
			&&
			is_array($options) && Arr::get($value, 'widget') === 'associate_categories') {
			$table_name = Arr::get($options, 'table');
			if (!$idOnly && Schema::hasTable($table_name)) {
				return Category::whereIn(
					'id',
					DB::table($table_name)
						->where(['mid' => $this->id])
						->pluck('cid')
						->all()
				)->get();
			}

			if ($idOnly && Schema::hasTable($table_name)) {
				return DB::table($table_name)
					->select('cid')
					->where(['mid' => $this->id])
					->pluck('cid')
					->toArray();
			}
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		$arr = parent::toArray();
		if (isset($this->translatedAttributes)) {

			/** @var Translatable $this */
			if (RoutingUtility::isAdmin()) {
				$fallback_locale = config('app.fallback_locale');
				foreach (getLocales() as $locale) {
					if ($translations = $this->getTranslation($locale)) {
						foreach ($this->translatedAttributes as $field) {
							$arr[$locale][$field] = $translations->$field;

							if ($locale === $fallback_locale) {
								$arr[$field] = $translations->$field;
							}
						}
					}
				}

			} elseif ($translations = $this->getTranslation()) {
				foreach ($this->translatedAttributes as $field) {
					$arr[$field] = $translations->$field;
				}
			}
		}
		return $arr;
	}

	/**
	 * @return array collection des champs du model si fillable tel qu'inscrit dans l'array fillable
	 * ou la collection des champs de la base de données
	 */
	public function getFormFields(): array
	{

		if (isset($this->fillable)) {

			$fields = $this->fillable;

			if (in_array('password', $fields)) {
				array_splice(
					$fields,
					array_search('password', $fields) + 1,
					0,
					'password_confirmation'
				); // splice position 3
			}

			if ($this->restricted) {
				/** @var User $user */
				$user = Auth::guard('users')->user();

				foreach ($this->restricted as $key => $value) {
					if ($user->name && !in_array($user->name, $value, true)) {
						$fields = array_diff($fields, array($key));
					}
				}
			}
		} else {

			/** @noinspection PhpUndefinedFieldInspection */
			$fields = Arr::pluck($this->model->describe(), 'Field');
			$fields = array_diff($fields, array(
				'id',
				'position',
				'created_at',
				'updated_at'
			));
		}

		return $fields;
	}

	/**
	 * @param string $field nom de la propriété
	 * @return mixed Retourn le type de la correspondance d'une propriété dans la base de données
	 */
	public function getFieldType($field)
	{

		if (is_array($this['fieldTypes'])) {
			$cast = Arr::get($this['fieldTypes'], $field, false);
			if ($cast) {
				return $cast;
			}
		}

		if (is_array($this->translatedAttributes) && in_array($field, $this->translatedAttributes, true)) {
			/** @var Translatable $this */
			$translationModel = $this->getTranslationModelName();
			$describe = describe(new $translationModel());
		} else {
			$describe = describe($this);
		}

		$j = Arr::pluck($describe, 'Field');
		$k = Arr::pluck($describe, 'Type');

		$key = array_search($field, $j, true);

		return $k[$key];
	}

	/**
	 * @param $field
	 * @return null
	 */
	public function getFieldPlaceholder($field)
	{
		return null;
	}

	public function saveElement($data = null, $isUnguard = false)
	{
		$isUpdate = $this->exists;

		$isAjax = Request::wantsJson();

		$success = false;

		if (($isAjax) && (!$this->isAjaxEnabled)) {
			App::abort(401);
		} //verification si autorisé à faire des requêtes ajax

		if ($isUnguard || $this->validate($data)) {

			// Collecte les champs faisant appel aux associations de catégories par table
			$associate_categories = [];
			$associate_entities = [];
			foreach ($this->customFields as $key => $value) {
				$options = Arr::get($value, 'options');

				if (Arr::get($value, 'widget') === 'associate_categories'
					&&
					array_key_exists($key, $data)
					&&
					is_array($options)
					&&
					!empty($options['table'])) {
					$associate_categories[$key] = $data[$key];
					//if(!empty($this->$key))  $data[$key] = implode(', ', Category::getAllByIds(explode(',', $this->$key))->lists('title'));
					unset($data[$key]);
				}

				if (Arr::get($value, 'widget') === 'associate_entities'
					&&
					array_key_exists($key, $data)
				) {
					$associate_entities[$key] = $data[$key];
					unset($data[$key]);
				}
			}

			$data = $this->parseFileUploads($data);
			foreach (getLocales() as $locale) {
				if (isset($data[$locale])) {
					$data[$locale] = $this->parseFileUploads($data[$locale], $locale);
				}
			}

			// if (isset($data['page'])) {
			// 	unset($data['page'], $data['children']);
			// }

			//si mot de passe vide en mode update
			if (
				isset($data['password'])
				&&
				($isUpdate)
				&&
				($data['password'] === '')
				&&
				($data['password_confirmation'] === '')
			) {
				unset($data['password']);
			}

			$data = Arr::except($data, [
				'_method',
				'_token'
			]); //retrait de ces éléments qui ne servent pas...

			// sanitize price fields
			$data = $this->sanitizePriceFields($data);

			if (count($data)) {
				$describe = $this->describe();
				$fields = Arr::pluck($describe, 'Field');

				foreach ($data as $key => $value) {

					$fieldName = '';
					$val = null;

					$keyVar = array_search($key, $fields, true);
					$fieldDescription = false;

					if ($keyVar !== false) {
						$fieldDescription = Arr::get($describe, $keyVar, false);
					}

					if (Str::contains($key, '-datetime')) {

						$fieldName = str_replace('-datetime', '', $key);
						$val = Arr::get($data, $fieldName) . ' ' . $value;

						if ($val === ' ') {
							$val = null;
						}

					} elseif ($key === 'filename') { //type de fichier

						if (Str::contains($this->$key, 'http')) {
							$identifier = StringUtility::getYouTubeIdFromUrl($this->$key);
							if (!empty($identifier)) {
								$fieldName = $key;
								$val = $identifier;
							}
						}

					} elseif (
						is_array($fieldDescription)
						&&
						$fieldDescription['Null'] === 'YES'
						&&
						Str::startsWith($fieldDescription['Type'], 'int(')
					) {
						if ($value === '') {
							$fieldName = $key;
						}
					}

					//si trouvé .. traitement
					if (!empty($fieldName) && $this->isPropertyExists($fieldName)) {
						$data[$fieldName] = $val;
					}
				}
			}

			$this->fill($data);
			$success = $this->save();

			if ($success) {

				if ($this->is_bloc) {
					/** @var App\Models\Core\Blocs\BlocModel $this */
					if ($this->bloc) {
						/** @noinspection PhpUndefinedFieldInspection */
						$this->active = Arr::get($data, 'active', $this->active);
						$this->bloc->save();
					} else {
						Bloc::create(array_merge([
							'blocable_id' => $this->id,
						], $data));
					}
				}

				// Association de catégories par table
				foreach ($associate_categories as $key => $value) {
					$customField = $this->customFields[$key];
					$options = Arr::get($customField, 'options');
					$table_name = Arr::get($options, 'table');
					$this->associateCategories($table_name, $value);
				}

				foreach ($associate_entities as $key => $value) {
					$customField = $this->customFields[$key];
					$options = Arr::get($customField, 'options');

					$values = preg_split('/,/', $value, -1, PREG_SPLIT_NO_EMPTY);

					$relationName = $options['relation'];
					/** @var BelongsToMany $relation */
					$relation = $this->{$relationName}();

					$relation->sync($values);
				}
			}
		}

		if ($isAjax) {
			if ($success) {
				return $this;
			}

			App::abort(500);
		}

		return $success;
	}

	/**
	 * @param array $data Collection des Input
	 * @return true or false à savoir si le formulaire est valide ou pas
	 */
	public function validate(&$data): bool
	{

		//		$isAjax = Request::wantsJson();
		$isUpdate = Request::isMethod('put');
		$isAdmin = RoutingUtility::isAdmin();

		// if (($isAjax) && ($isUpdate)) return true;

		$primaryKeyName = $this->getKeyName();
		$rules = $this->rules;
		$niceNames = $this->niceNames;

		if (!$isAdmin && isset($this->rulesFrontEnd)) {
			$rules = $this->rulesFrontEnd;
		}

		if ($isAdmin && isset($this->translatedAttributes)) {
			foreach ($this->translatedAttributes as $key) {
				if (isset($rules[$key])) {
					$value = $rules[$key];

					foreach (getLocales() as $locale) {
						$key2 = $locale . '.' . $key;
						$rules[$key2] = $value;
						$niceNames[$key2] = Arr::get(
								$niceNames,
								$key,
								__('properties.' . $key)
							) . ' (' . $locale . ')';
					}
					unset($rules[$key]);
				}
			}
		}

		foreach ($rules as $key => $value) { //traitements de validation

			if (
				Str::contains($value, 'required')
				&&
				(Str::contains($key, 'image')
					||
					Str::contains($key, 'document'))
			) {
				if (($this->$primaryKeyName) || (Request::hasFile($key))) { //si update ou un fichier est présent en input-file ou Ajax
					$rules[$key] = str_replace('required', '', $value); //on retire le required des règles
				}
			}

			if (($key === 'g-recaptcha-response') && (RoutingUtility::isAdmin())) { //retrait du captcha en mode admin
				unset($rules[$key]);
			}

			if ($isUpdate && ($key === 'password' || $key === 'password_confirmation')) {
				unset($rules[$key]);
			}

			$value = str_replace('{id}', $this->id, $value);
			/*if (count(Request::all()) && (Str::contains($value, "unique"))) { //UNIQUE RULES

			if (Str::contains($value, "unique:users")) { //email - users
			$value = str_replace("unique:users", "unique:users,email," . $this->id . ",id", $value);
			}

			}*/
			$rules[$key] = $value;
		}

		$v = Validator::make($data, $rules);
		$v->setAttributeNames($niceNames);

		if ($v->fails()) {
			$this->errors = $v;

			return false;
		}

		if (!isset($data['password_confirmation'])) {
			unset($data['password']);
		} elseif (isset($data['password']) && $data['password'] !== $data['password_confirmation']) {
			Session::flash('error', Lang::get(
				'validation.confirmed',
				array('attribute' => __('properties.password_confirmation'))
			));

			return false;
		}
		return true;
	}

	protected function parseFileUploads($data, $locale = null)
	{
		$result = [];
		foreach ($data as $key => $value) {
			$fullkey = $locale ? $locale . '.' . $key : $key;
			if (
				!Str::contains($key, 'associate')
				&&
				!Str::endsWith($key, '_id')
				&&
				(
					Str::endsWith($key, [
						'image',
						'image-remove'
					])
					||
					Str::endsWith($key, [
						'photo',
						'photo-remove'
					])
					||
					Str::endsWith($key, 'document')
					||
					Str::endsWith($key, [
						'_file',
						'file-remove'
					])
					||
					$key === 'file'
					||
					(!RoutingUtility::isAdmin() && Str::endsWith($key, ['filename']))
				)
			) { //Traitement des éléments multimédias - upload
				$str_value = $data[$key];

				if (Str::contains($key, '-remove')) {

					$result[str_replace('-remove', '', $key)] = '';
					unset($data[$key]);

				} elseif (
					(strpos($str_value, 'base64') > -1)
					&&
					(!str_contains($key, 'image_name'))
				) { //FICHER EN BASE 64

					if (!empty($data[$key . '_name'])) {
						if (strpos($data[$key . '_name'], 'removeMedia:') > -1) {
							$result = Arr::add($result, $key, '');
							$fname = pathinfo($data[$key . '_name'], PATHINFO_FILENAME);
							$this->removeImage($fname . '.jpg', $key);
							continue;
						}

						if ($data[$key . '_name'] !== 'randomName') {
							$fname = pathinfo($data[$key . '_name'], PATHINFO_FILENAME);
						}
					}

					if (empty($fname)) { //générer un nom unique pour le fichier
						$fname = StringUtility::generateRandomString(12);
					}

					try {
						$e = $this->saveImageBase64($str_value, $fname, $key);
						$result = Arr::add($result, $key, $e);
					} catch (Exception|Error $e) {
						return Response::json([
							'fname'     => $fname,
							'str_value' => $str_value,
							'key'       => $key,
							'error'     => $e->getMessage(),
						]);
					}
				} elseif (count(Request::all())) { //FICHIER UPLOADER VIA FORMULAIRE
					if (!empty($data[$fullkey]) && !is_string($data[$fullkey])) {
						$result[$key] = $this->saveMedia($data[$fullkey], $key, 'single', $locale);
					} elseif (Request::hasFile($fullkey)) {
						$result[$key] = $this->saveMedia(Request::file($fullkey), $key, 'single', $locale);
					} elseif (is_string($data[$fullkey])) {
						$result[$key] = $data[$fullkey];
					} else {
						$data = Arr::except(
							$data,
							[$key]
						); //On retire pour la mise à jour d'un uploader....
					}
				}
			}
		}
		return array_merge($data, $result);
	}

	private function sanitizePriceFields($data)
	{
		$priceFields = array_keys(array_filter($data, static function ($key) {
			return Str::endsWith($key, '_price');
		}, ARRAY_FILTER_USE_KEY));

		foreach ($priceFields as $field) {
			$data[$field] = str_replace(array(
				',',
				' '
			), '', $data[$field]);
		}

		return $data;
	}

	public function associateCategories($table_name, $associate_category, $clear = true)
	{
		if (empty(static::$associateCategoriesTableExists[$table_name]) && !Schema::hasTable($table_name)) {
			Schema::create($table_name, function (Blueprint $table) {
				$table->id();
				$table->foreignId('mid')->unsigned()->index();
				$table->foreign('mid')->references('id')->on($this->getTable())->onDelete('cascade');
				$table->foreignId('cid')->unsigned()->index();
				$table->foreign('cid')->references('id')->on('categories')->onDelete('cascade');
				$table->timestamp('created_at')->nullable();
				$table->timestamp('updated_at')->nullable();
			});
			static::$associateCategoriesTableExists[$table_name] = true;
		}
		$mid = $this->id;
		if ($clear) {
			DB::table($table_name)->where(['mid' => $mid])->delete();
		}
		if (!empty($associate_category)) {
			$entries = [];
			$categories = is_string($associate_category) ? explode(',', $associate_category) : $associate_category;
			$i = count($categories);
			while ($i-- > 0) {
				$cid = (int)$categories[$i];
				$entries[] = [
					'mid'        => $mid,
					'cid'        => $cid,
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now(),
				];
			}
			DB::table($table_name)->insert($entries);
		}
	}

	public function getAssociateEntitiesByCategory($attribute, $category_identifier)
	{
		$isWidget = Arr::get($this->customFields, $attribute . '.widget') === 'associate_categories';
		$table_name = Arr::get($this->customFields, $attribute . '.options.table');
		$group_identifier = Arr::get($this->customFields, $attribute . '.options.identifier');

		$category = Category::getListByIdentifier($group_identifier)->where('identifier',
			$category_identifier)->first();

		if ($category !== null && $isWidget && Schema::hasTable($table_name)) {
			return $this::whereIn(
				'id',
				DB::table($table_name)->where(['cid' => $category->id])->get()->pluck('mid')
			)->get();
		}
		return new Collection();
	}

	public function fieldIsRequired($field): bool
	{
		return array_key_exists($field, $this->rules);
	}

	/**
	 * Traitement des changements de position AJAX (API) uniquement
	 *
	 * @param  [array] ensemble de propriétés pour le traitement
	 * @return Exception|int 200 pour confirmation - erreur si pas le cas
	 */
	public function changeOrder($data)
	{
		$items = Arr::get($data, 'items');
		$relation = Arr::get($data, 'relation');
		$relation_attribute = Arr::get($data, 'relation_attribute');
		$relation_id = Arr::get($data, 'relation_id');

		$isRelation = !empty($relation_attribute);

		$relation_classname = $isRelation ? ModelUtility::getClassByName($relation) : null;

		try {
			$table = $this->getTable();

			/** @noinspection SqlWithoutWhere */
			$query = "UPDATE `{$table}` SET `position` = CASE `id`";

			$i = 0;
			$count = count($items);
			while ($i < $count) {
				$query .= " WHEN {$items[$i++]} THEN {$i}";
			}

			$query .= ' END';

			if ($isRelation) {
				if ($table === 'blocs') {
					$escapedClassName = str_replace("\\", "\\\\", $relation_classname);
					$query .= " WHERE `pageable_id` = {$relation_id} AND `pageable_type` = '$escapedClassName'";
				} else {
					$query .= " WHERE `{$relation_attribute}` = {$relation_id}";
				}
			}

			DB::statement($query);

			if ($this->isCache) {
				$this->deleteCache();
			}

			if ($isRelation && class_exists($relation_classname)) {
				$model = new $relation_classname(); //jeter le cache de la collection associée
				if ($model->isCache) {
					$model->deleteCache();
				}
			}
		} catch (Exception|Error $e) {
			return $e;
		}

		return 200;
	}

	/**
	 * [setHeadline Mise à l'avant (sélection) d'un élément d'une collection]
	 *
	 * @param [type] $data [post Request::all]
	 * @return int
	 */
	public function setHeadline(): int
	{

		$relation = Request::get('relation');
		$relation_id = Str::singular($relation) . '_id';
		$refId = Request::get('refId');
		$id = Request::get('id');

		$sql = "UPDATE {$this->getTable()} SET is_headline = 0 WHERE {$relation_id} = {$refId} AND id <> {$id};";
		$sql1 = "UPDATE {$this->getTable()} SET is_headline = 1 WHERE id={$id};";
		DB::statement($sql);
		DB::statement($sql1);

		if ($this->isCache) {

			$this->deleteCache();
			$parent_class_name = ModelUtility::getClassByName($relation);

			if (class_exists($parent_class_name)) {
				$m = new $parent_class_name();
				if ($m->isCache) {
					$m->deleteCache();
				}
			}
		}

		return 1;
	}

	/**
	 *
	 */
	public function recalculate(): void
	{
	}

	/**
	 * @return bool
	 */
	public function isSubgridExportable(): bool
	{
		return !empty($this->exports['generic']);
	}

	/**
	 * Determine if the given attribute exists.
	 *
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return $this->getAttribute($offset) !== null || isset($this->$offset);
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset): mixed
	{
		return $this->getAttribute($offset) ?? $this->$offset;
	}

	/**
	 * Set the value for a given offset.
	 *
	 * @param mixed $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value): void
	{
		if ($this->getAttribute($offset) !== null) {
			$this->setAttribute($offset, $value);
		} else {
			$this->$offset = $value;
		}
	}

	/**
	 * Unset the value for a given offset.
	 *
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset): void
	{
		if ($this->getAttribute($offset) !== null) {
			unset($this->attributes[$offset], $this->relations[$offset]);
		} else {
			unset($this->$offset);
		}
	}


	/**
	 * Scope a query to only include active.
	 *
	 * @param Builder $query
	 * @return Builder|self
	 */
	public function scopeActive($query): Builder
	{
		if (in_array('active', $this->fillable, true)) {
			$table = $this->getTable();
			return $query->where("{$table}.active", true);
		}

		return $query;
	}

	public function getCollectionNameAttribute()
	{
		$reflect = new ReflectionClass(static::class);
		return Str::snake(Str::pluralStudly($reflect->getShortName()));
	}

	public function isBigData()
	{
		return $this->bigData;
	}

	/**
	 * @return SearchResult
	 */
	public function getSearchResultAttribute(): SearchResult
	{
		$result = new SearchResult();
		$result->label = $this->title;
		$result->url = $this->url;

		return $result;
	}
}

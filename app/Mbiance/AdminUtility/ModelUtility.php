<?php

namespace Mbiance\AdminUtility;

use Arr;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Request;
use Route;
use Session;
use Str;

class ModelUtility
{
	private $classmap;

	public function __construct()
	{
		$this->classmap = config('classmap');
	}

	public function getClassByName($name)
	{
		return $this->getClassByCollectionName($name);
	}

	public function getAddItemLabel()
	{
		return __('admin.additem', ['attr' => $this->labelFromProperty('singular')]);
	}

	private function labelFromProperty(string $property): string
	{
		$object = $this->getChildModel();
		return $object->$property ?? ' éléments';
	}

	public function getBackToListLabel()
	{
		return __(
			'admin.backtolist',
			['det' => __('admin.det'), 'attr' => $this->labelFromProperty('relatedGrid')]
		);
	}

	public function getChildModel()
	{

		/** @var HasMany|BelongsToMany|HasOne $relationObj */
		if ($relationObj = Session::get('admin.relationObj')) {

			if ($relationObj instanceof BelongsToMany) {
				return  $relationObj->newPivot();
			}

			return ($relationObj->getRelated());
		}

		$class = $this->getChildrenClassNameFromRoute();
		return new $class();
	}

	public function getCollectionNameFromClassName($name)
	{
		// Utilisation de la config classmap pour retrouver la collection
		$key = array_search($name, $this->classmap, true);
		if ($key === false) {
			$keyArr = explode('\\', $name);
			$key = end($keyArr);
			$key = Str::snake(Str::pluralStudly($key));
		}

		return $key;
	}

	public function getResourceNameFromClassName($name)
	{
		$keyArr = explode('\\', $name);
		$key = end($keyArr);

		return Str::snake($key);
	}


	public function getClassNameFromRoute()
	{
		return $this->getClassByCollectionName(Request::segment(2));
	}

	public function getCollectionNameFromRoute()
	{
		return Request::segment(2);
	}

	/**
	 * Look up class name of a collection
	 *
	 * @param  string  $name  Nom de la collection
	 * @return string       Nom de la classe en PascalCase
	 * Exemple : va convertir galleryelements en GalleryElement
	 */
	public function getClassByCollectionName($name)
	{
		if (!$name) {
			return;
		}

		$needle = Str::plural($name);

		$class = Arr::get($this->classmap, $needle, false);
		if ($class) {
			return $class;
		}

		$className = Str::studly($name);
		$coreClass = "App\Models\Core\\{$className}";
		$regularClass = "App\Models\\{$className}";

		return class_exists($regularClass) ? $regularClass : $coreClass;
	}

	public function getChildrenClassNameFromRoute()
	{
		$parent = $this->getClassNameFromRoute();

		return get_class(
			(new $parent)
				->{$this->getChildrenCollectionNameFromRoute()}()
				->getRelated()
		);
	}

	public function getChildrenCollectionNameFromRoute()
	{
		$onglet = Route::input('onglet', '');

		return Str::camel($onglet);
	}

	public function csv_to_array($filename = '', $delimiter = ';', $header = null, $isFirstRowHeader = false)
	{
		/**
		 *
		 * POUR TRAITEMENT DE FICHIERS CSV PROVENANT POSSIBLEMENT DE MAC
		 *
		 * activer l'option de configuration auto_detect_line_endings.
		 *
		 */
		ini_set('auto_detect_line_endings', true);

		if (!is_file($filename) || !is_readable($filename)) {
			return false;
		}

		$data = array();
		if (($handle = fopen($filename, 'r')) !== false) {
			$count = 0;
			while (($row = fgetcsv($handle, 0, $delimiter, '"')) !== false) {

				if ((!$isFirstRowHeader) || ($count > 0)) {
					$data[] = array_combine($header, $row);
				}

				$count++;
			}

			fclose($handle);
		}
		return $data;
	}


	public function isFieldRequired($field, $rules)
	{
		if (preg_match('/\w+\[([\w\d_]+)\]/', $field, $matches)) {
			$field = $matches[1];
		}

		$rule = Arr::get($rules, $field);
		return $rule && strpos($rule, 'required') > -1;
	}
}

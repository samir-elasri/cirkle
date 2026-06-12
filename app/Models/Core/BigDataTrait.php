<?php


namespace App\Models\Core;


use Arr;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection as SupportCollection;
use Session;
use Str;

trait BigDataTrait
{

	/**
	 * Get a range of results
	 * Used for BigData and BigData search
	 *
	 * @param null|int $page
	 * @param null|int $length
	 * @param null|string $sort
	 * @param null|string $order
	 * @param null|string $search
	 * @param null|Model $parentModel
	 * @param null|string $relation
	 * @return Collection|SupportCollection
	 */
	public static function getRange(
		$page,
		$length,
		$sort,
		$order,
		$search = null,
		$parentModel = null,
		$relation = null
	)
	{
		/**
		 * Start Query
		 * If Onglet, get relation, else start new query
		 */
		if (isset($parentModel, $relation)) {
			/** @var Relation|Builder $query */
			$query = $parentModel->$relation();
			$model = $query->getRelated();
			$table = $model->getTable();

			$query = $query->select("{$table}.*");
		} else {
			$model = new static();
			$table = $model->getTable();
			$query = $model::select("{$table}.*");
		}

		if (method_exists(static::class, $methodName = 'filterGetRange')) {
			static::$methodName($query);
		}

		/** @var Model $model */
		$grid = $model->grid; # Array of shown fields
		$joins = []; # Join array helps prevent joining same table multiple times

		// Join translation table
		$t_table = self::parseTranslatables($model, $table, $query, $joins);

		// If search value is defined, search for it in all grid fields in a nested where
		if (isset($grid)) {
			$query = $query->where(static function ($subQuery) use (
				$order,
				$sort,
				$t_table,
				$grid,
				$model,
				$search,
				&$query,
				$table,
				&$joins
			) {
				$isSearch = !empty($search);
				$with = [];

				/** @var Builder $subQuery */
				foreach ($grid as $field) {
					if (Str::contains($field, '.')) {
						[$relationName, $column] = explode('.', $field, 2);

						if ($table !== $relationName) {
							$with[] = $relationName;
						}
					}

					$isSort = $field === $sort;

					$model->searchField($model, $field, $subQuery, $search, $table, $t_table, $query, $order, $isSort,
						$isSearch, $joins);
				}

				$query->with($with);
			});
		}

		$total = (clone $query)->selectRaw("count(distinct $table.id) as aggregate ")->pluck('aggregate')[0];
		$query = $query->groupBy("{$table}.id");

		$model->sortField($model, $sort, $query, $table, $t_table, $order, $joins);

		$query = $query->offset(($page - 1) * $length)->limit($length);
		$collections = $query->get();
		$collections->total = $total;

		Session::put('grid-state', [
			'model'    => get_class($model),
			'relation' => '',
			'data'     => [
				'sort'      => $sort,
				'sortIndex' => array_search($sort, $model['grid'], true),
				'order'     => $order,
				'page'      => $page,
				'search'    => $search,
				'length'    => $length,
			],
		]);

		return $collections;
	}

	/**
	 * @param Model $model
	 * @param string $table
	 * @param Builder|QueryBuilder $query
	 * @param array|null $joins
	 * @param bool $sameClass
	 * @param null $alias
	 * @return string
	 */
	protected static function parseTranslatables(
		$model,
		string $table,
		&$query,
		&$joins,
		$sameClass = false,
		$alias = null
	)
	{
		$t_table = null; # translation table

		// If has translations
		if (in_array(Translatable::class, class_uses($model), true)) {

			/** @var Model|Translatable $model */
			$trans = $model->getTranslationModelName();

			/** @var Model $t */
			$t = new $trans();
			$t_table = $t->getTable();
			$joinTable = $t_table;

			if ($sameClass) {
				$t_table = "{$alias}_translations";
				$joinTable .= "as $t_table";
			}

			$related_id = $model->getTranslationRelationKey();
			$joinKey = $model->getJoinKey($table, $t_table);

			if (!in_array($joinKey, $joins, true)) {
				$query = $query->leftJoin($joinTable, "{$t_table}.{$related_id}", '=', "{$table}.id");
				$joins[] = $joinKey;
			}
		}
		return $t_table;
	}

	/**
	 * @param Model $model
	 * @param string|array $field
	 * @param Builder $subQuery
	 * @param string $search
	 * @param string $table
	 * @param string|null $t_table
	 * @param Builder $query
	 * @param string $order
	 * @param bool $isSort
	 * @param bool $isSearch
	 * @param array $joins
	 */
	protected function searchField(
		$model,
		$field,
		$subQuery,
		$search,
		$table,
		$t_table,
		$query,
		$order,
		$isSort,
		$isSearch,
		array &$joins
	): void
	{
		if (mb_stripos($field, '.') !== false) {
			//If it has a dot we try to look for a relation
			$model->searchRelation($subQuery, $field, $model, $query, $search, $order, $table, $t_table, $isSort,
				$isSearch, $joins);

		} elseif ($isSearch) {
			if ($gridField = Arr::get($model['gridFields'], $field, false)) {
				// If the field exists in gridfields as a key, use it's corresponding value either as column name or aggregate function (ex: CONCAT_WS)
				$model->searchGridField($subQuery, $model, $gridField, $search, $table, $t_table, $query, $order,
					$isSort,
					$isSearch, $joins);

			} elseif (array_key_exists($field, $model->enum)) {
				// If is enum, find the proper key
				$model->searchEnum($subQuery, $model, $field, $search, $table);

			} elseif (is_array($field)) {
				// If the field is an array, consider all the value as attributes for current model.
				$model->searchArray($subQuery, $field, $model, $t_table, $search, $table);

			} elseif (
				$t_table !== null
				&&
				isset($model['translatedAttributes'])
				&&
				in_array($field, $model['translatedAttributes'], true)
			) {
				// Translation attribute
				$subQuery->orWhere("{$t_table}.{$field}", 'LIKE', "%{$search}%");
			} else {
				// Is (hopefully) an attribute of the model
				$subQuery->orWhere("{$table}.{$field}", 'LIKE', "%{$search}%");
			}
		}
	}

	/**
	 * @param Model $model
	 * @param string|array $sort
	 * @param Builder $query
	 * @param string $table
	 * @param string|null $t_table
	 * @param string $order
	 * @param array $joins
	 */
	protected function sortField(
		$model,
		$sort,
		$query,
		$table,
		$t_table,
		$order,
		&$joins
	): void
	{
		if (is_array($sort)) {
			// If the field is an array, consider all the value as attributes for current model.
			$model->sortArray($query, $sort, $model, $t_table, $table, $order);

		} elseif ($gridField = Arr::get($model['gridFields'], $sort, false)) {
			// If the field exists in gridfields as a key, use it's corresponding value either as column name or aggregate function (ex: CONCAT_WS)
			$model->sortGridField($query, $model, $gridField, $order, $table, $t_table, $sort, $joins);

		} elseif (mb_stripos($sort, '.') !== false) {
			//If it has a dot we try to look for a relation
			$model->sortRelation($query, $sort, $model, $order, $table, $t_table, $joins);

		} elseif (array_key_exists($sort, $model->enum)) {
			// If is enum, find the proper key
			$model->sortEnum($query, $model, $sort, $order, $table);

		} elseif ($t_table !== null
			&&
			isset($model['translatedAttributes'])
			&&
			in_array($sort, $model['translatedAttributes'], true)) {
			// Translation attribute
			$query->orderBy("{$t_table}.{$sort}", $order);
		} else {
			// Is (hopefully) an attribute of the model
			$query->orderBy("{$table}.{$sort}", $order);
		}
	}

	/**
	 * @param Builder $query
	 * @param $model
	 * @param $sort
	 * @param $order
	 * @param $table
	 */
	public function sortEnum($query, $model, $sort, $order, $table): void
	{
		$orderStr = "CASE ";
		$enum = $model->enum[$sort];
		natsort($enum);

		$i = 0;
		foreach ($enum as $key => $value) {
			$orderStr .= "WHEN {$table}.{$sort} = '$key' THEN $i ";
			$i++;
		}

		$query->orderByRaw("{$orderStr} END $order");
	}

	/**
	 * @param $model_table
	 * @param $relation_table
	 * @return string
	 */
	public function getJoinKey($model_table, $relation_table): string
	{
		return "{$model_table}:{$relation_table}";
	}

	/**
	 * @param Builder $subQuery
	 * @param string $field
	 * @param Model $model
	 * @param Model|Builder $query
	 * @param string $search
	 * @param string $order
	 * @param string $table
	 * @param string $t_table
	 * @param bool $isSort
	 * @param bool $isSearch
	 * @param array $joins
	 */
	public function searchRelation(
		$subQuery,
		$field,
		$model,
		$query,
		$search,
		$order,
		$table,
		$t_table,
		$isSort,
		$isSearch,
		&$joins
	): void
	{
		[$relationName, $column] = explode('.', $field, 2);

		// Safeguard against dev overqualifying field
		if (in_array($relationName, [$table, $t_table], true)) {
			$model->searchField($model, $column, $subQuery, $search, $table, $t_table, $query,
				$order, $isSort, $isSearch, $joins);
			return;
		}

		// if the relation exists
		$relation = $model->$relationName();

		if ($isSearch || $isSort) {

			// Supports BelongsTo and partially hasMany
			if ($relation instanceof MorphTo) {

				// Get relation model, table and two keys
				foreach (Arr::get($model['morphClasses'], $relationName) as $class) {
					/** @var Model $relatedModel */
					$relatedModel = new $class();
					$relatedTable = $relatedModel->getTable();

					$joinTable = $relatedTable;

					if (get_class($relatedModel) === get_class($model)) {
						$relatedTable = $relationName;
						$joinTable .= " as $relatedTable";
					}

					$related_id = $relation->getForeignKeyName();

					$joinKey = $model->getJoinKey($table, $relatedTable);

					if (!in_array($joinKey, $joins, true)) {
						// Join related table
						$query->leftJoin($joinTable, "{$table}.{$related_id}", '=', "{$relatedTable}.id");
						$joins[] = $joinKey;
					}

					$related_t_table = self::parseTranslatables($relatedModel, $relatedTable, $query, $joins);

					// Go back through search logic
					$model->searchField($relatedModel, $column, $subQuery, $search, $relatedTable, $related_t_table,
						$query, $order, $isSort, $isSearch, $joins);
				}

			} elseif ($relation instanceof BelongsTo) {
				// Get relation model, table and two keys
				/** @var Model $relatedModel */
				$relatedModel = $relation->getRelated();
				$relatedTable = $relatedModel->getTable();

				$joinTable = $relatedTable;

				if (get_class($relatedModel) === get_class($model)) {
					$relatedTable = $relationName;
					$joinTable .= " as $relatedTable";
				}

				$related_id = $relation->getForeignKeyName();

				$joinKey = $model->getJoinKey($table, $relatedTable);

				if (!in_array($joinKey, $joins, true)) {
					// Join related table
					$query->leftJoin($joinTable, "{$table}.{$related_id}", '=', "{$relatedTable}.id");
					$joins[] = $joinKey;
				}

				$related_t_table = self::parseTranslatables($relatedModel, $relatedTable, $query, $joins);


				// Go back through search logic
				$model->searchField($relatedModel, $column, $subQuery, $search, $relatedTable, $related_t_table, $query,
					$order, $isSort, $isSearch, $joins);

			} elseif ($relation instanceof HasMany || $relation instanceof HasOne) {
				/** @var Model $relatedModel */
				$relatedModel = $relation->getRelated();
				$relatedTable = $originalRelatedTable = $relatedModel->getTable();
				$joinTable = $relatedTable;

				if (get_class($relatedModel) === get_class($model)) {
					$relatedTable = $relationName;
					$joinTable .= " as $relatedTable";
				}

				$related_id = $relation->getForeignKeyName();

				$joinKey = $model->getJoinKey($table, $relatedTable);

				if (!in_array($joinKey, $joins, true)) {
					// Join related table
					$query->leftJoin($joinTable, static function ($join) use ($originalRelatedTable, $relation, $table, $related_id, $relatedTable) {
						$join->on("{$relatedTable}.{$related_id}", '=', "{$table}.id");

						if ($relation instanceof HasOne) {
							// Force latest
							$join->whereRaw("{$relatedTable}.id IN (
								SELECT MAX({$relatedTable}2.id)
								FROM {$originalRelatedTable} AS {$relatedTable}2
								WHERE {$relatedTable}2.$related_id = {$table}.id
							)");
						}
					});

					$joins[] = $joinKey;
				}

				/**
				 * Check if property or dynamic variable (property todo)
				 */
				if ($column === "?" || $column === "count") {
					// Alias name
					$alias = "{$relationName}Count";
					$query->addSelect(DB::raw("COUNT({$relatedTable}.{$related_id}) as {$alias}"));

					// Check if it has conditions
					$clauses = "{$relationName}Clauses";
					if (method_exists($model, $clauses)) {
						$model->$clauses($query);
					}

					//					$query->orHaving(DB::raw("COUNT({$relatedTable}.{$related_id})"), $search);

					// Dynamic Variable
					if ($isSort) {
						$query->orderBy($alias, $order);
					}
				}

				if ($relation instanceof HasOne) {
					$related_t_table = self::parseTranslatables($relatedModel, $relatedTable, $query, $joins);

					// Go back through search logic
					$model->searchField($relatedModel, $column, $subQuery, $search, $relatedTable, $related_t_table, $query,
						$order, $isSort, $isSearch, $joins);
				}
			}
		}
	}

	/**
	 * @param Builder $subQuery
	 * @param Model $model
	 * @param string|array $gridField
	 * @param string $search Query string
	 * @param string $table Model's table
	 * @param string $t_table Translation Table
	 * @param Model|Builder $query
	 * @param string $order
	 * @param bool $isSort
	 * @param bool $isSearch
	 * @param array $joins
	 */
	public function searchGridField(
		$subQuery,
		$model,
		$gridField,
		$search,
		$table,
		$t_table,
		$query,
		$order,
		$isSort,
		$isSearch,
		&$joins
	): void
	{
		if (is_array($gridField)) {
			$model->searchArray($subQuery, $gridField, $model, $t_table, $search, $table);

		} elseif (mb_stripos($gridField, '.') !== false && mb_stripos($gridField, ' ') === false) {
			//If it has a dot we try to look for a relation
			$model->searchRelation($subQuery, $gridField, $model, $query, $search, $order, $table, $t_table, $isSort,
				$isSearch, $joins);

		} else {

			// If it isn't an array, we assume it is an sql function
			$subQuery->orWhere(DB::raw($gridField), 'LIKE', "%{$search}%");
		}
	}

	/**
	 * @param Builder $subQuery
	 * @param $model
	 * @param $field
	 * @param $search
	 * @param $table
	 */
	public function searchEnum($subQuery, $model, $field, $search, $table): void
	{
		foreach ($model->enum[$field] as $key => $value) {
			// if string is in enum values
			if (mb_stripos($value, $search) !== false) {
				// Add where query for the enum key
				$subQuery->orWhere("{$table}.{$field}", '=', $key);
			}
		}
	}

	/**
	 * @param Builder $subQuery
	 * @param array $field
	 * @param Model $model
	 * @param string|null $t_table
	 * @param string|null $search
	 * @param string $table
	 */
	public function searchArray($subQuery, $field, $model, $t_table, $search, $table): void
	{
		[$concatFields, $size] = $model->getConcatFields($model, $field, $t_table, $table);

		if ($size > 0) {
			if ($size === 1) {
				//If only one value, we treat as if it wasn't in an array
				$subQuery->orWhere(DB::raw(reset($concatFields)), 'LIKE', "%{$search}%");

			} else {
				// Concat all values to be able to use with CONCAT_WS
				$concatField = implode(', ', $concatFields);
				$subQuery->orWhere(DB::raw("CONCAT_WS(' ', {$concatField})"), 'LIKE', "%{$search}%");
			}
		}
	}

	/**
	 * @param Builder $subQuery
	 * @param Model $model
	 * @param string|array $gridField
	 * @param string $order
	 * @param string $table Model's table
	 * @param string $t_table Translation Table
	 * @param string $sort
	 * @param array $joins
	 */
	public function sortGridField($subQuery, $model, $gridField, $order, $table, $t_table, $sort, $joins): void
	{
		if (is_array($gridField)) {
			$model->sortArray($subQuery, $gridField, $model, $t_table, $table, $order);

		} elseif (mb_stripos($gridField, '.') !== false && mb_stripos($gridField, ' ') === false) {
			//If it has a dot we try to look for a relation
			$model->sortRelation($subQuery, $gridField, $model, $order, $table, $t_table, $joins);

		} else {
			// If it isn't an array, we assume it is an sql function
			$subQuery->orderByRaw("{$gridField} {$order}");
		}
	}

	/**
	 * @param Model|Builder $query
	 * @param string $sort
	 * @param Model $model
	 * @param string $order
	 * @param string $table
	 * @param string $t_table
	 * @param array $joins
	 */
	public function sortRelation(
		$query,
		$sort,
		$model,
		$order,
		$table,
		$t_table,
		&$joins
	): void
	{
		[$relation, $column] = explode('.', $sort, 2);

		// Safeguard against dev overqualifying sort
		if (in_array($relation, [$table, $t_table], true)) {
			$model->sortField($model, $column, $query, $table, $t_table, $order, $joins);
			return;
		}

		// if the relation exists
		$relationName = $relation;
		$relation = $model->$relation();

		// Supports BelongsTo and partially hasMany
		if ($relation instanceof MorphTo) {
			// Get relation model, table and two keys
			foreach (Arr::get($model['morphClasses'], $relationName) as $class) {
				/** @var Model $relatedModel */
				$relatedModel = new $class();
				$relatedTable = $relatedModel->getTable();

				// If it is a relation that uses the same model, we used the relation as an alias
				if (get_class($relatedModel) === get_class($model)) {
					$relatedTable = $relationName;
				}

				$related_t_table = self::parseTranslatables($relatedModel, $relatedTable, $query, $joins);

				// Go back through search logic
				$model->sortField($relatedModel, $column, $query, $relatedTable, $related_t_table, $order, $joins);
			}

		} elseif ($relation instanceof BelongsTo || $relation instanceof HasOne) {
			// Get relation model, table and two keys
			/** @var Model $relatedModel */
			$relatedModel = $relation->getRelated();
			$relatedTable = $relatedModel->getTable();

			// If it is a relation that uses the same model, we used the relation as an alias
			if (get_class($relatedModel) === get_class($model)) {
				$relatedTable = $relationName;
			}

			$related_t_table = self::parseTranslatables($relatedModel, $relatedTable, $query, $joins);

			// Go back through search logic
			$model->sortField($relatedModel, $column, $query, $relatedTable, $related_t_table, $order, $joins);

		}
	}

	/**
	 * @param Builder $subQuery
	 * @param array $sort
	 * @param Model $model
	 * @param string|null $t_table
	 * @param string $table
	 * @param string|null $order
	 */
	public function sortArray($subQuery, $sort, $model, $t_table, $table, $order): void
	{
		// Check all fields to specify it's table
		[$concatFields, $size] = $model->getConcatFields($model, $sort, $t_table, $table);

		if ($size > 0) {
			if ($size === 1) {
				//If only one value, we treat as if it wasn't in an array
				$subQuery->orderByRaw(reset($concatFields) . " {$order}");
			} else {
				// Concat all values to be able to use with CONCAT_WS
				$concatField = implode(', ', $concatFields);
				$subQuery->orderByRaw("CONCAT_WS(' ', {$concatField}) {$order}");
			}
		}
	}

	/**
	 * @param Model $model
	 * @param array $field
	 * @param string|null $t_table
	 * @param string $table
	 * @return array
	 */
	public function getConcatFields($model, $field, $t_table, $table): array
	{
		$concatFields = [];
		foreach ($field as $key => $item) {
			if (is_int($key)) {
				if (
					$t_table !== null
					&&
					isset($model['translatedAttributes'])
					&&
					in_array($item, $model['translatedAttributes'], true)
				) {
					$concatFields[] = "{$t_table}.{$item}";
				} else {
					$concatFields[] = "{$table}.{$item}";
				}

			} else {
				$concatFields[] = $item;
			}
		}

		$size = count($concatFields);
		return array(
			$concatFields,
			$size
		);
	}
}

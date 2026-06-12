<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use RoutingUtility;

class AdminScope implements Scope
{

	/**
	 * @inheritDoc
	 */
	public function apply(Builder $builder, Model $model)
	{
		/** @var \App\Models\Core\Model $model */
		if (
			!$model->isBigData()
			&&
			RoutingUtility::isAdmin()
			&&
			$model->isFieldExists($model->order_default)) { //vérifier si la propriété existe au niveau de la base de données
			$order = $model->order_default;

			if ($order === 'id') {
				$order = $model->getTable().'.id';
			}

			return $builder->orderBy($order, $model->order_direction);
		}

		return $builder;
	}
}

<?php

namespace App\Models\Core;

use Arr;
use Request;
use Route;

class Onglet
{

	public static function findOnglets($identifiant)
	{
		$elements = array();
		$items = (new static())->find($identifiant);

		if ($items != null) {

			$selected = Route::input('onglet');
			$selected = $selected ? str_replace('_form', '', $selected) : null;
			$segment = Request::segment(3);

			$i = 0;

			foreach ($items as $item) {
				$identifiant = Arr::get($item, 'identifiant');
				$relation = Arr::get($item, 'relation');
				$create = Arr::get($item, 'create', false);

				if (
					($create && ($segment === 'create'))
					||
					($segment !== 'create')
				) {

					$push = true;

					/** @noinspection SuspiciousBinaryOperationInspection */
					if (
						($selected === null && $i === 0)
						||
						($selected !== null && in_array($selected, [$identifiant, $relation], true))
					) {
						$item['active'] = true;
					} else {
						$item['active'] = false;
					}

					if ($identifiant === 'pages') {

						/** @var Model|Page $model */
						$model = app('formutility')->model;

						if ($model->integrated) {
							$config = config('routes.front-end');
							$route = Arr::get($config, $model->label, []);
							$admin = Arr::get($route, 'admin', []);

							switch ($item->identifiant) {
								case 'blocs':
									$push = Arr::get($admin, 'blocs', true);
									break;
								case 'sharing':
									$push = Arr::get($route, 'admin', true);
									break;
							}
						}
					}

					if ($push) {
						$elements[] = $item;
						$i++;
					}
				}
			}
		}

		return $elements;
	}

	public function find($identifiant)
	{
		return config("onglet.$identifiant");
	}
}

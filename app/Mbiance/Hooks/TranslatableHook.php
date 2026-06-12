<?php

declare(strict_types=1);

namespace App\Mbiance\Hooks;

use App\Models\Core\Translatable;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Barryvdh\LaravelIdeHelper\Contracts\ModelHookInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

class TranslatableHook implements ModelHookInterface
{
	public function run(ModelsCommand $command, Model $model): void
	{
		if (
			!property_exists($model, 'translatedAttributes')
			||
			!method_exists($model, 'getTranslationModelName')
			||
			!in_array(Translatable::class, class_uses($model), true)
		) {
			return;
		}

		$className = $model->getTranslationModelName();

		/** @var Model $modelTranslation */
		$modelTranslation = $command->getLaravel()->make($className);

		$table = $modelTranslation->getConnection()->getTablePrefix() . $modelTranslation->getTable();
		$schema = $modelTranslation->getConnection()->getDoctrineSchemaManager();
		$databasePlatform = $schema->getDatabasePlatform();
		$databasePlatform->registerDoctrineTypeMapping('enum', 'string');

		$platformName = $databasePlatform->getName();


		foreach (config("ide-helper.custom_db_types.{$platformName}", []) as $yourTypeName => $doctrineTypeName) {
			$databasePlatform->registerDoctrineTypeMapping($yourTypeName, $doctrineTypeName);
		}

		$database = null;
		if (strpos($table, '.')) {
			[$database, $table] = explode('.', $table);
		}

		$columns = $schema->listTableColumns($table, $database);

		if (!$columns) {
			return;
		}

		foreach ($columns as $column) {
			$name = $column->getName();

			if (!in_array($name, $model->translatedAttributes, true)) {
				continue;
			}

			if (($dates = $modelTranslation->getDates()) && in_array($name, $dates, true)) {
				$type = $this->getDateClass();
			} else {
				$type = $column->getType()->getName();
				switch ($type) {
					case 'string':
					case 'text':
					case 'date':
					case 'time':
					case 'guid':
					case 'datetimetz':
					case 'datetime':
					case 'decimal':
						$type = 'string';
						break;
					case 'integer':
					case 'bigint':
					case 'smallint':
						$type = 'integer';
						break;
					case 'boolean':
						switch (config('database.default')) {
							case 'sqlite':
							case 'mysql':
								$type = 'integer';
								break;
							default:
								$type = 'boolean';
								break;
						}
						break;
					case 'float':
						$type = 'float';
						break;
					default:
						$type = 'mixed';
						break;
				}
			}

			$comment = $column->getComment();
			$command->setProperty(
				$name,
				$type,
				true,
				true,
				$comment,
				true,
			);
		}
	}


	protected function getDateClass(): string
	{
		return class_exists(Date::class)
			? '\\' . get_class(Date::now())
			: Carbon::class;
	}
}

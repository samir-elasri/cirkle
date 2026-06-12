<?php

namespace App\Console\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Str;

class MigrationCreator extends \Illuminate\Database\Migrations\MigrationCreator
{
	/**
	 * The custom app stubs directory.
	 *
	 * @var string
	 */
	protected $customStubPath;

	/**
	 * Create a new migration at the given path.
	 *
	 * @param  string  $name
	 * @param  string  $path
	 * @param  string|null  $table
	 * @param  bool  $create
	 * @return string
	 *
	 * @throws \Exception
	 */
	public function createTranslated($name, $path, $table = null, $create = false, $translated = false)
	{
		$this->ensureMigrationDoesntAlreadyExist($name, $path);

		// First we will get the stub file for the migration, which serves as a type
		// of template for the migration. Once we have those we will populate the
		// various place-holders, save the file, and run the post create event.
		$stub = $this->getStubTranslated($table, $create, $translated);

		$path = $this->getPath($name, $path);

		$this->files->ensureDirectoryExists(dirname($path));

		$this->files->put(
			$path, $this->populateStub($stub, $table)
		);

		// Next, we will fire any hooks that are supposed to fire after a migration is
		// created. Once that is done we'll be ready to return the full path to the
		// migration file so it can be used however it's needed by the developer.
		$this->firePostCreateHooks($table, $path);

		return $path;
	}

	/**
	 * Get the migration stub file.
	 *
	 * @param string|null $table
	 * @param bool $create
	 * @return string
	 * @throws FileNotFoundException
	 */
	protected function getStubTranslated($table, $create, $translated)
	{
		if (is_null($table)) {
			$stub = $this->files->exists($customPath = $this->customStubPath.'/migration.stub')
				? $customPath
				: $this->stubPath().'/migration.stub';
		} elseif ($translated) {
			$stub = $this->files->exists($customPath = $this->customStubPath.'/migration.translated.stub')
				? $customPath
				: $this->stubPath().'/migration.translated.stub';
		} elseif ($create) {
			$stub = $this->files->exists($customPath = $this->customStubPath.'/migration.create.stub')
				? $customPath
				: $this->stubPath().'/migration.create.stub';
		} else {
			$stub = $this->files->exists($customPath = $this->customStubPath.'/migration.update.stub')
				? $customPath
				: $this->stubPath().'/migration.update.stub';
		}

		return $this->files->get($stub);
	}

	/**
	 * Populate the place-holders in the migration stub.
	 *
	 * @param  string  $stub
	 * @param  string|null  $table
	 * @return string
	 */
	protected function populateStub($stub, $table)
	{
		$tableSingular = Str::singular($table);

		// Here we will replace the table place-holders with the table specified by
		// the developer, which is useful for quickly creating a tables creation
		// or update migration from the console instead of typing it manually.
		if (! is_null($table)) {
			$stub = str_replace(
				[
					'DummyTable',
					'{{ table }}',
					'{{table}}',
					'{{ singular }}',
					'{{singular}}'
				],
				[
					$table,
					$table,
					$table,
					$tableSingular,
					$tableSingular
				],
				$stub);
		}

		return $stub;
	}
}

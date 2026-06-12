<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as OriginalCommand;
use Illuminate\Database\Console\Migrations\TableGuesser;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class MigrateMakeCommand extends OriginalCommand
{
	protected $creator = MigrationCreator::class;

	/**
	 * The console command signature.
	 *
	 * @var string
	 */
	protected $signature = 'make:migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration (Deprecated)}
        {--translatable : Creates a translation table too}';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->creator = app('migration.creator');

		// It's possible for the developer to specify the tables to modify in this
		// schema operation. The developer may also specify if this table needs
		// to be freshly created so we can create the appropriate migrations.
		$name = Str::snake(trim($this->input->getArgument('name')));

		$table = $this->input->getOption('table');

		$create = $this->input->getOption('create') ?: false;

		// If no table was given as an option but a create option is given then we
		// will use the "create" option as the table name. This allows the devs
		// to pass a table name into this option as a short-cut for creating.
		if (!$table && is_string($create)) {
			$table = $create;

			$create = true;
		}

		// Next, we will attempt to guess the table name if this the migration has
		// "create" in the name. This will allow us to provide a convenient way
		// of creating migrations that create new tables for the application.
		if (!$table) {
			[$table, $create] = TableGuesser::guess($name);
		}

		// Now we are ready to write the migration out to disk. Once we've written
		// the migration out, we will dump-autoload for the entire framework to
		// make sure that the migrations are registered by the class loaders.
		$this->writeMigration($name, $table, $create);
	}

	/**
	 * Write the migration file to disk.
	 *
	 * @param string $name
	 * @param string $table
	 * @param bool $create
	 * @return void
	 * @throws Exception
	 */
	protected function writeMigration($name, $table, $create)
	{
		$translated = $this->input->getOption('translatable') ?: false;

		$file = $this->creator->createTranslated(
			$name, $this->getMigrationPath(), $table, $create, $translated
		);

		$this->components->info(sprintf('Migration [%s] created successfully.', $file));
	}
}

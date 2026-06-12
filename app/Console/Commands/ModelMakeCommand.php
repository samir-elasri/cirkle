<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\ModelMakeCommand as OriginalModelMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:model')]
class ModelMakeCommand extends OriginalModelMakeCommand
{

	/**
	 * Execute the console command.
	 *
	 * @return void|false
	 */
	public function handle()
	{
		if (parent::handle() === false && !$this->option('force')) {
			return false;
		}

		if ($this->option('translatable')) {
			$this->createTranslationModel();
		}
	}

	/**
	 * Create a translation model for the model
	 * @return void
	 */
	protected function createTranslationModel(): void
	{
		$name = Str::studly($this->argument('name'));

		$this->call('make:translation', [
			'name' => "{$name}Translation"
		]);
	}

	/**
	 * Create a migration file for the model.
	 *
	 * @return void
	 */
	protected function createMigration(): void
	{
		$table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

		if ($this->option('pivot')) {
			$table = Str::singular($table);
		}

		$this->call('make:migration', [
			'name'           => "create_{$table}_table",
			'--create'       => $table,
			'--fullpath'     => true,
			'--translatable' => $this->option('translatable'),
		]);
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub(): string
	{
		if ($this->option('pivot')) {
			return $this->resolveStubPath('/stubs/model.pivot.stub');
		}

		if ($this->option('morph-pivot')) {
			return $this->resolveStubPath('/stubs/model.morph-pivot.stub');
		}

		if ($this->option('translatable')) {
			return $this->resolveStubPath('/stubs/model.translated.stub');
		}

		return $this->resolveStubPath('/stubs/model.stub');
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions(): array
	{
		return [
			['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, policy, resource controller, and form request classes for the model'],
			['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
			['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
			['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
			['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
			['morph-pivot', null, InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom polymorphic intermediate table model'],
			['policy', null, InputOption::VALUE_NONE, 'Create a new policy for the model'],
			['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
			['pivot', 'p', InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom intermediate table model'],
			['resource', 'r', InputOption::VALUE_NONE, 'Indicates if the generated controller should be a resource controller'],
			['api', null, InputOption::VALUE_NONE, 'Indicates if the generated controller should be an API resource controller'],
			['requests', 'R', InputOption::VALUE_NONE, 'Create new form request classes and use them in the resource controller'],
			['translatable', 't', InputOption::VALUE_NONE, 'Create a new translation model for the model'],
		];
	}
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Laravel\Prompts\multiselect;

#[AsCommand(name: 'make:translation')]
class TranslationModelMakeCommand extends GeneratorCommand
{
	use CreatesMatchingTest;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:translation';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new translation model class';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'TranslationModel';

	/**
	 * Execute the console command.
	 *
	 * @return void|bool
	 */
	public function handle()
	{
		if (parent::handle() === false && ! $this->option('force')) {
			return false;
		}
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return $this->resolveStubPath('/stubs/model.translation.stub');
	}

	/**
	 * Resolve the fully-qualified path to the stub.
	 *
	 * @param  string  $stub
	 * @return string
	 */
	protected function resolveStubPath($stub)
	{
		return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
			? $customPath
			: __DIR__.$stub;
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return is_dir(app_path('Models\\Translations')) ? $rootNamespace.'\\Models\\Translations' : $rootNamespace;
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}
}

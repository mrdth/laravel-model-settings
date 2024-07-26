<?php

namespace Mrdth\LaravelModelSettings\Commands;

use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Schema;
use Mrdth\LaravelModelSettings\Services\ModelFinderService;

class MakeModelSettingsMigrationCommand extends BaseCommand implements PromptsForMissingInput
{
    public $signature = 'make:msm {name : The name of the model}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}';

    public $description = 'Create a new migration to add the model settings column to a model';

    protected MigrationCreator $creator;

    /**
     * The Composer instance.
     */
    protected Composer $composer;

    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->creator = new MigrationCreator(new Filesystem, __DIR__.'/../stubs/');
        $this->composer = $composer;
    }

    public function handle()
    {
        // Store the model name for later use.
        $model_candidates = (new ModelFinderService)->getModel($this->input->getArgument('name'));

        if ($model_candidates->isEmpty()) {
            $this->error('Model not found.');

            return;
        }

        if ($model_candidates->count() > 1) {
            $model = $this->choice(
                'Multiple models found. Which model do you want to add settings to?',
                $model_candidates->toArray()
            );
        } else {
            $model = $model_candidates->first();
        }

        $table = (new $model)->getTable();

        $column = config('model-settings.column', 'settings');
        if (Schema::hasColumn($table, $column)) {
            $this->error("The '{$column}' column already exists on the '{$table}' table.");

            return;
        }

        // Set the name for the migration.
        $name = "add_settings_column_to_{$table}_table";

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($name, $table);

        $this->composer->dumpAutoloads();
    }

    protected function writeMigration($name, $table): void
    {
        $file = $this->creator->create(
            $name, $this->getMigrationPath(), $table, false
        );

        $this->components->info(sprintf('Migration [%s] created successfully.', $file));
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     */
    protected function getMigrationPath(): string
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                ? $this->laravel->basePath().'/'.$targetPath
                : $targetPath;
        }

        return parent::getMigrationPath();
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => 'What is the Model you want to add settings to?',
        ];
    }
}

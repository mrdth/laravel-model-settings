<?php

namespace Mrdth\LaravelModelSettings\Tests;

use Mrdth\LaravelModelSettings\LaravelModelSettingsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelModelSettingsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-model-settings_table.php.stub';
        $migration->up();
        */
    }
}

<?php

namespace Mrdth\LaravelModelSettings;

use Mrdth\LaravelModelSettings\Commands\MakeModelSettingsMigrationCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelModelSettingsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-model-settings')
            ->hasConfigFile()
            ->hasCommand(MakeModelSettingsMigrationCommand::class);
    }
}

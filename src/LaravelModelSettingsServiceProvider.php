<?php

namespace Mrdth\LaravelModelSettings;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Mrdth\LaravelModelSettings\Commands\LaravelModelSettingsCommand;

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
            ->hasCommand(LaravelModelSettingsCommand::class);
    }
}

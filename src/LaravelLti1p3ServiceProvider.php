<?php

namespace Wien\LaravelLti1p3;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wien\LaravelLti1p3\Commands\LaravelLti1p3Command;

class LaravelLti1p3ServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-lti1p3')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-lti1p3_table')
            ->hasCommand(LaravelLti1p3Command::class);
    }
}

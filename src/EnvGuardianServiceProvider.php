<?php

namespace OpenLaravel\EnvGuardian;

use OpenLaravel\EnvGuardian\Commands\CheckEnvCommand;
use OpenLaravel\EnvGuardian\Commands\DiffEnvCommand;
use OpenLaravel\EnvGuardian\Commands\DiscoverEnvCommand;
use OpenLaravel\EnvGuardian\Commands\InstallGitHookCommand;
use OpenLaravel\EnvGuardian\Commands\SyncEnvExampleCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EnvGuardianServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('env-guardian')
            ->hasConfigFile('env-guardian')
            ->hasCommands([
                SyncEnvExampleCommand::class,
                DiffEnvCommand::class,
                CheckEnvCommand::class,
                InstallGitHookCommand::class,
                DiscoverEnvCommand::class,
            ]);
    }
}

<?php

namespace OpenLaravel\EnvGuardian\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use OpenLaravel\EnvGuardian\EnvGuardianServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'OpenLaravel\\EnvGuardian\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            EnvGuardianServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}

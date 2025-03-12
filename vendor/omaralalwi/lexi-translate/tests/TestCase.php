<?php

namespace Omaralalwi\LexiTranslate\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Omaralalwi\LexiTranslate\LexiTranslateServiceProvider;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [
            LexiTranslateServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadLaravelMigrations();
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}

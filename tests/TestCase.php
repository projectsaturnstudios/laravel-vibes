<?php

namespace ProjectSaturnStudios\Vibes\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use ProjectSaturnStudios\Vibes\Providers\LaravelVibesServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelVibesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Add any environment configuration needed for tests
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        
        // Configure Vibes for testing
        $app['config']->set('vibes.service_info.server_name', 'test-vibes-server');
        $app['config']->set('vibes.service_info.catch_exceptions', true);
        $app['config']->set('vibes.auto_discover_all_primitives', []);
        // Override cache path for testing to use storage path
        $app['config']->set('vibes.service_info.cache_path', storage_path('framework/testing/cache'));
    }
} 
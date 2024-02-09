<?php

namespace NotificationChannels\Bluesky\Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use NotificationChannels\Bluesky\BlueskyServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BlueskyServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        Http::preventStrayRequests();

        /** @var Repository */
        $config = $app->make(Repository::class);
        $config->set('services.bluesky.username', 'bsky-username'); // env('BLUESKY_USERNAME')
        $config->set('services.bluesky.password', 'bsky-password'); // env('BLUESKY_PASSWORD')
    }

    protected function resolveApplication(): Application
    {
        return (new Application($this->getBasePath()))
            ->useEnvironmentPath(__DIR__ . '/..');
    }
}

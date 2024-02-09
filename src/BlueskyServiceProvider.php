<?php

namespace NotificationChannels\Bluesky;

use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Client\Factory as HttpClient;
use NotificationChannels\Bluesky\IdentityRepository\IdentityRepository;
use NotificationChannels\Bluesky\IdentityRepository\IdentityRepositoryUsingCache;
use NotificationChannels\Bluesky\SessionManager\SessionManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class BlueskyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('bluesky-notification-channel');
    }

    public function boot(): void
    {
        $this->app->singleton(IdentityRepository::class, fn () => new IdentityRepositoryUsingCache(
            cache: $this->app->make(Cache::class),
        ));

        $this->app->singleton(BlueskyClient::class, fn () => new BlueskyClient(
            httpClient: $this->app->make(HttpClient::class),
            baseUrl: $this->app->make(Config::class)->get('services.bluesky.base_url', default: BlueskyClient::DEFAULT_BASE_URL),
            username: $this->app->make(Config::class)->get('services.bluesky.username'),
            password: $this->app->make(Config::class)->get('services.bluesky.password'),
        ));

        $this->app->singleton(SessionManager::class, fn () => new SessionManager(
            client: $this->app->make(BlueskyClient::class),
            identityRepository: $this->app->make(IdentityRepository::class),
        ));

        $this->app->singleton(BlueskyService::class, fn () => new BlueskyService(
            client: $this->app->make(BlueskyClient::class),
            identityRepository: $this->app->make(IdentityRepository::class),
            sessionManager: $this->app->make(SessionManager::class),
        ));
    }
}

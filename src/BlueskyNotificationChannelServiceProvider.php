<?php

namespace Innocenzi\BlueskyNotificationChannel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Innocenzi\BlueskyNotificationChannel\Commands\BlueskyNotificationChannelCommand;

class BlueskyNotificationChannelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('bluesky-notification-channel')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_bluesky-notification-channel_table')
            ->hasCommand(BlueskyNotificationChannelCommand::class);
    }
}

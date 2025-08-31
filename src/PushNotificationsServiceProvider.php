<?php

namespace Xentixar\FilamentPushNotifications;

use Xentixar\FilamentPushNotifications\Console\Commands\StartSockeonCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PushNotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-push-notifications')
            ->hasCommands(StartSockeonCommand::class);
    }
}
<?php

namespace Xentixar\FilamentPushNotifications;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Xentixar\FilamentPushNotifications\Console\Commands\StartSockeonCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PushNotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-push-notifications')
            ->hasCommands(StartSockeonCommand::class)
            ->hasMigrations('create_push_notifications_table')
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $installCommand) {
                $installCommand
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hello, and welcome to Filament Push Notifications!');
                        $command->newLine(1);
                    })
                    ->publishMigrations();
            });
    }

    public function boot(): void
    {
        parent::boot();

        $this->publishes([
            __DIR__ . '/../database/migrations/create_push_notifications_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_push_notifications_table.php'),
        ], 'filament-push-notifications-migrations');
    }
}
<?php

namespace Xentixar\FilamentPushNotifications;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Xentixar\FilamentPushNotifications\Console\Commands\StartSockeonCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xentixar\FilamentPushNotifications\Console\Commands\GenerateVapidKeysCommand;
use Xentixar\FilamentPushNotifications\Services\WebPushService;

class PushNotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-push-notifications')
            ->hasCommands([
                StartSockeonCommand::class,
                GenerateVapidKeysCommand::class,
            ])
            ->hasMigrations([
                'create_push_notifications_table',
                'create_push_subscriptions_table',
            ])
            ->hasConfigFile('filament-push-notifications')
            ->runsMigrations()
            ->hasViews()
            ->hasRoute('web')
            ->hasInstallCommand(function (InstallCommand $installCommand) {
                $installCommand
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hello, and welcome to Filament Push Notifications!');
                        $command->newLine(1);
                    })
                    ->publishMigrations()
                    ->publishConfigFile();
            });
    }

    public function boot(): void
    {
        parent::boot();

        // Register WebPushService as singleton
        $this->app->singleton(WebPushService::class);

        $this->publishes([
            __DIR__ . '/../database/migrations/create_push_notifications_table.php.stub' => database_path('migrations/2024_01_01_000001_create_push_notifications_table.php'),
        ], 'filament-push-notifications-migrations');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_push_subscriptions_table.php.stub' => database_path('migrations/2024_01_01_000002_create_push_subscriptions_table.php'),
        ], 'filament-push-notifications-migrations');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-push-notifications');

        $this->publishes([
            __DIR__ . "/../resources/views" => resource_path('views/vendor/filament-push-notifications'),
        ], 'filament-push-notifications-views');

        $this->publishes([
            __DIR__ . '/../config/filament-push-notifications.php' => config_path('filament-push-notifications.php'),
        ], 'filament-push-notifications-config');

        // Publish service worker to public directory
        $this->publishes([
            __DIR__ . '/../resources/js/service-worker.js' => public_path('service-worker.js'),
        ], 'filament-push-notifications-assets');

        Filament::serving(function () {
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn(): string => view('filament-push-notifications::notification')->render(),
            );
        });
    }
}
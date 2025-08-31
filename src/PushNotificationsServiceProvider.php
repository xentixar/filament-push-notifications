<?php

namespace Xentixar\FilamentPushNotifications;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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
            ->hasConfigFile('filament-push-notifications')
            ->runsMigrations()
            ->hasViews()
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

        $this->publishes([
            __DIR__ . '/../database/migrations/create_push_notifications_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_push_notifications_table.php'),
        ], 'filament-push-notifications-migrations');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-push-notifications');

        $this->publishes([
            __DIR__ . "/../resources/views" => resource_path('views/vendor/filament-push-notifications'),
        ], 'filament-push-notifications-views');

        $this->publishes([
            __DIR__ . '/../config/filament-push-notifications.php' => config_path('filament-push-notifications.php'),
        ], 'filament-push-notifications-config');

        Filament::serving(function () {
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament-push-notifications::notification')->render(),
            );
        });
    }
}
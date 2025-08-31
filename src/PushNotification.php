<?php

namespace Xentixar\FilamentPushNotifications;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PushNotification implements Plugin
{
    public static function make()
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-push-notifications';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([

        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
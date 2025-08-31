<?php

namespace Xentixar\FilamentPushNotifications\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum PushNotificationType: string implements HasLabel, HasColor, HasIcon
{
    case BROWSER = 'browser';
    case FILAMENT = 'filament';

    public function getLabel(): string
    {
        return match ($this) {
            self::BROWSER => 'Browser',
            self::FILAMENT => 'Filament'
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::BROWSER => 'success',
            self::FILAMENT => 'info'
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::BROWSER => 'heroicon-o-globe-alt',
            self::FILAMENT => 'heroicon-o-check-circle'
        };
    }
}

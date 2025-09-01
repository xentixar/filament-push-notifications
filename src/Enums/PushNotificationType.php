<?php

namespace Xentixar\FilamentPushNotifications\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum PushNotificationType: string implements HasLabel, HasColor, HasIcon
{
    case NATIVE = 'native';
    case LOCAL = 'local';

    public function getLabel(): string
    {
        return match ($this) {
            self::NATIVE => 'Native',
            self::LOCAL => 'Local'
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NATIVE => 'success',
            self::LOCAL => 'info'
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::NATIVE => 'heroicon-o-globe-alt',
            self::LOCAL => 'heroicon-o-check-circle'
        };
    }
}

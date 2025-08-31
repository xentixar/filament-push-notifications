<?php

namespace Xentixar\FilamentPushNotifications\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum PushNotificationType: string implements HasLabel, HasColor, HasIcon
{
    case INFO = 'info';
    case SUCCESS = 'success';
    case ERROR = 'error';

    public function getLabel(): string
    {
        return match ($this) {
            self::INFO => 'Info',
            self::SUCCESS => 'Success',
            self::ERROR => 'Error',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::INFO => 'info',
            self::SUCCESS => 'success',
            self::ERROR => 'error',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::INFO => 'heroicon-o-information-circle',
            self::SUCCESS => 'heroicon-o-check-circle',
            self::ERROR => 'heroicon-o-x-circle',
        };
    }
}

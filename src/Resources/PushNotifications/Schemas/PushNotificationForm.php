<?php

namespace Xentixar\FilamentPushNotifications\Resources\PushNotifications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Xentixar\FilamentPushNotifications\Enums\PushNotificationType;
use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;

class PushNotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Title')
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('message')
                    ->columnSpanFull()
                    ->label('Message')
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options(PushNotificationType::class)
                    ->columnSpanFull()
                    ->required(),
                Select::make('receivers')
                    ->label('Receivers')
                    ->options(fn() => config('filament-push-notifications.receiver_model', User::class)::all()->pluck('name', 'id'))
                    ->multiple()
                    ->columnSpanFull()
                    ->preload()
                    ->searchable()
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->minDate(now())
                    ->label('Scheduled At')
                    ->live()
                    ->helperText('Leave empty to send immediately. Timezone is ' . config('app.timezone') . '.')
                    ->columnSpanFull(),
                TextEntry::make('scheduled_at_difference')
                    ->state(fn(Get $get): string => $get('scheduled_at') ? 'The notification will be sent in ' . (int) now()->diffInSeconds($get('scheduled_at')) . ' seconds.' : '')
                    ->label('Scheduled At Difference')
                    ->visible(fn(Get $get) => $get('scheduled_at'))
                    ->columnSpanFull(),
            ]);
    }
}

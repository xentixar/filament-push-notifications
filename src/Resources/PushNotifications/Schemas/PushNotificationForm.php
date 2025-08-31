<?php

namespace Xentixar\FilamentPushNotifications\Resources\PushNotifications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Xentixar\FilamentPushNotifications\Enums\PushNotificationType;
use App\Models\User;

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
                    ->options(fn() => User::all()->pluck('name', 'id'))
                    ->multiple()
                    ->columnSpanFull()
                    ->preload()
                    ->searchable()
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled At')
                    ->helperText('Leave empty to send immediately')
                    ->columnSpanFull(),
            ]);
    }
}

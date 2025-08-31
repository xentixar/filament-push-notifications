<?php

namespace Xentixar\FilamentPushNotifications\Resources\PushNotifications;

use Xentixar\FilamentPushNotifications\Resources\PushNotifications\Pages\ListPushNotifications;
use Xentixar\FilamentPushNotifications\Resources\PushNotifications\Schemas\PushNotificationForm;
use Xentixar\FilamentPushNotifications\Resources\PushNotifications\Tables\PushNotificationsTable;
use Xentixar\FilamentPushNotifications\Models\PushNotification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PushNotificationResource extends Resource
{
    protected static ?string $model = PushNotification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PushNotificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PushNotificationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPushNotifications::route('/'),
        ];
    }
}

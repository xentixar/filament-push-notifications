<?php

namespace Xentixar\FilamentPushNotifications\Resources\PushNotifications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\User;

class PushNotificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('message')
                            ->label('Message'),
                    ])
                    ->columns(1),
                Section::make('Notification Information')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('type')
                            ->badge()
                            ->label('Type'),
                        TextEntry::make('receivers')
                            ->label('Receivers')
                            ->badge()
                            ->formatStateUsing(function ($state) {
                                $userIds = explode(',', $state);
                                $users = config('filament-push-notifications.receiver_model', User::class)::query()->whereIn('id', $userIds)->take(3)->get();
                                return $users->map(function ($user) {
                                    return $user->name;
                                })->implode(', ');
                            }),
                    ])
                    ->columns(2),
                Section::make('Date Information')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('scheduled_at')
                            ->default('N/A')
                            ->label('Scheduled At'),
                        TextEntry::make('created_at')
                            ->default('N/A')
                            ->label('Created At'),
                    ])
                    ->columns(2),
            ]);
    }
}

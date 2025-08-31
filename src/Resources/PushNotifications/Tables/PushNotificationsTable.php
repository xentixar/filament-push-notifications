<?php

namespace Xentixar\FilamentPushNotifications\Resources\PushNotifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Xentixar\FilamentPushNotifications\Enums\PushNotificationType;
use App\Models\User;

class PushNotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('message')
                    ->limit(30)
                    ->label('Message')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('receivers')
                    ->label('Receivers')
                    ->searchable()
                    ->badge()
                    ->limit(10)
                    ->formatStateUsing(function ($state) {
                        $userIds = explode(',', $state);
                        $users = User::query()->whereIn('id', $userIds)->take(3)->get();
                        return $users->map(function ($user) {
                            return $user->name;
                        })->implode(', ');
                    }),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled At')
                    ->default('N/A')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(PushNotificationType::class),
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

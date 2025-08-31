<?php

namespace Xentixar\FilamentPushNotifications\Resources\PushNotifications\Pages;

use Filament\Actions\CreateAction;
use Xentixar\FilamentPushNotifications\Jobs\SchedulePushNotificationJob;
use Xentixar\FilamentPushNotifications\Resources\PushNotifications\PushNotificationResource;
use Filament\Resources\Pages\ListRecords;
use Xentixar\FilamentPushNotifications\Models\PushNotification;

class ListPushNotifications extends ListRecords
{
    protected static string $resource = PushNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function (PushNotification $record) {
                    if ($record->scheduled_at && $record->scheduled_at->isFuture()) {
                        $delay = now()->diffInSeconds($record->scheduled_at);
                        SchedulePushNotificationJob::dispatch($record)->delay($delay);
                    } else {
                        SchedulePushNotificationJob::dispatchSync($record);
                    }
                })
        ];
    }
}

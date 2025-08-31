<?php

namespace Xentixar\FilamentPushNotifications\Resources\PushNotifications\Pages;

use Filament\Actions\CreateAction;
use Xentixar\FilamentPushNotifications\Events\NotificationPushedEvent;
use Xentixar\FilamentPushNotifications\Resources\PushNotifications\PushNotificationResource;
use Filament\Resources\Pages\ListRecords;
use Sockeon\Sockeon\Core\Event;

class ListPushNotifications extends ListRecords
{
    protected static string $resource = PushNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record) {
                    Event::broadcast(new NotificationPushedEvent($record));
                }),
        ];
    }
}

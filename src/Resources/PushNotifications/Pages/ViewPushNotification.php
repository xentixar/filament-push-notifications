<?php

namespace Xentixar\FilamentPushNotifications\Resources\PushNotifications\Pages;

use Xentixar\FilamentPushNotifications\Resources\PushNotifications\PushNotificationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPushNotification extends ViewRecord
{
    protected static string $resource = PushNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}

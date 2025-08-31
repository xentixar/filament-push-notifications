<?php

namespace Xentixar\FilamentPushNotifications\Jobs;

use Xentixar\FilamentPushNotifications\Models\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Sockeon\Sockeon\Core\Event;
use Xentixar\FilamentPushNotifications\Events\NotificationPushedEvent;

class SchedulePushNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly PushNotification $notification)
    {
        //
    }

    public function handle(): void
    {
        Event::broadcast(new NotificationPushedEvent($this->notification));
    }
}

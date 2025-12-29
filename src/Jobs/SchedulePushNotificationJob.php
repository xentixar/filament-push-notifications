<?php

namespace Xentixar\FilamentPushNotifications\Jobs;

use Xentixar\FilamentPushNotifications\Models\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Sockeon\Sockeon\Core\Event;
use Xentixar\FilamentPushNotifications\Events\NotificationPushedEvent;
use Xentixar\FilamentPushNotifications\Services\WebPushService;

class SchedulePushNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly PushNotification $notification)
    {
        //
    }

    public function handle(): void
    {
        // For native notifications, send via Web Push API
        if ($this->notification->type->value === 'native') {
            $webPushService = app(WebPushService::class);

            $payload = $webPushService::formatPayload(
                $this->notification->title,
                $this->notification->message,
                [
                    'icon' => config('filament-push-notifications.native_notification.favicon'),
                    'badge' => config('filament-push-notifications.native_notification.badge'),
                    'tag' => config('filament-push-notifications.native_notification.tag'),
                    'requireInteraction' => config('filament-push-notifications.native_notification.require_interaction'),
                    'silent' => config('filament-push-notifications.native_notification.silent'),
                    'vibrate' => config('filament-push-notifications.native_notification.vibrate'),
                ]
            );

            // Send to all receivers
            $webPushService->sendToUsers($this->notification->receivers, $payload);
        }

        // For local notifications (or as fallback), broadcast via WebSocket
        if ($this->notification->type->value === 'local') {
            Event::broadcast(new NotificationPushedEvent($this->notification));
        }
    }
}

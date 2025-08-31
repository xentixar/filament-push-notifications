<?php

namespace Xentixar\FilamentPushNotifications\Events;

use Sockeon\Sockeon\Contracts\WebSocket\EventableContract;
use Xentixar\FilamentPushNotifications\Models\PushNotification;

class NotificationPushedEvent implements EventableContract
{
    public function __construct(public readonly PushNotification $notification)
    {
        //
    }

    public function broadcastAs(): string
    {
        return 'notification.pushed';
    }

    public function broadcastNamespace(): ?string
    {
        return null;
    }

    public function broadcastOn(): ?array
    {
        $rooms = [];
        foreach ($this->notification->receivers as $receiver) {
            $rooms[] = 'user.' . $receiver;
        }
        return $rooms;
    }

    public function broadcastWith(): array
    {
        return [
            'notification' => [
                'title' => $this->notification->title,
                'message' => $this->notification->message,
                'type' => $this->notification->type,
            ]
        ];
    }
}

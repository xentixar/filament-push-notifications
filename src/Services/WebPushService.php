<?php

namespace Xentixar\FilamentPushNotifications\Services;

use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Xentixar\FilamentPushNotifications\Models\PushSubscription;

class WebPushService
{
    protected WebPush $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => config('filament-push-notifications.web_push.vapid_subject'),
                'publicKey' => config('filament-push-notifications.web_push.vapid_public_key'),
                'privateKey' => config('filament-push-notifications.web_push.vapid_private_key'),
            ],
        ];

        $this->webPush = new WebPush($auth);
    }

    /**
     * Send a notification to a single subscription.
     */
    public function sendNotification(PushSubscription $pushSubscription, array $payload): bool
    {
        if (!$pushSubscription->isValid()) {
            Log::warning('Invalid push subscription', ['subscription_id' => $pushSubscription->id]);
            return false;
        }

        $subscription = Subscription::create($pushSubscription->getSubscriptionArray());

        $options = [
            'TTL' => config('filament-push-notifications.web_push.ttl', 2419200),
            'urgency' => config('filament-push-notifications.web_push.urgency', 'normal'),
        ];

        if ($topic = config('filament-push-notifications.web_push.topic')) {
            $options['topic'] = $topic;
        }

        $this->webPush->queueNotification(
            $subscription,
            json_encode($payload),
            $options
        );

        $results = $this->webPush->flush();

        foreach ($results as $result) {
            if (!$result->isSuccess()) {
                // Handle expired subscriptions
                if ($result->isSubscriptionExpired()) {
                    Log::info('Subscription expired, deleting', ['subscription_id' => $pushSubscription->id]);
                    $pushSubscription->delete();
                    return false;
                }

                Log::error('Failed to send web push notification', [
                    'subscription_id' => $pushSubscription->id,
                    'reason' => $result->getReason(),
                ]);

                return false;
            }
        }

        return true;
    }

    /**
     * Send a notification to all subscriptions of a user.
     */
    public function sendToUser($user, array $payload): int
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();
        $successCount = 0;

        foreach ($subscriptions as $subscription) {
            if ($this->sendNotification($subscription, $payload)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Send a notification to multiple users.
     */
    public function sendToUsers(array $userIds, array $payload): int
    {
        $subscriptions = PushSubscription::whereIn('user_id', $userIds)->get();
        $successCount = 0;

        foreach ($subscriptions as $subscription) {
            $this->webPush->queueNotification(
                Subscription::create($subscription->getSubscriptionArray()),
                json_encode($payload),
                [
                    'TTL' => config('filament-push-notifications.web_push.ttl', 2419200),
                    'urgency' => config('filament-push-notifications.web_push.urgency', 'normal'),
                ]
            );
        }

        $results = $this->webPush->flush();

        foreach ($results as $result) {
            if ($result->isSuccess()) {
                $successCount++;
            } elseif ($result->isSubscriptionExpired()) {
                // Find and delete expired subscription
                $endpoint = $result->getEndpoint();
                PushSubscription::where('endpoint', $endpoint)->delete();
                Log::info('Deleted expired subscription', ['endpoint' => $endpoint]);
            } else {
                Log::error('Failed to send web push notification', [
                    'endpoint' => $result->getEndpoint(),
                    'reason' => $result->getReason(),
                ]);
            }
        }

        return $successCount;
    }

    /**
     * Format notification payload for web push.
     */
    public static function formatPayload(string $title, string $message, array $options = []): array
    {
        return [
            'title' => $title,
            'body' => $message,
            'icon' => $options['icon'] ?? config('filament-push-notifications.native_notification.favicon'),
            'badge' => $options['badge'] ?? config('filament-push-notifications.native_notification.badge'),
            'tag' => $options['tag'] ?? config('filament-push-notifications.native_notification.tag'),
            'requireInteraction' => $options['requireInteraction'] ?? false,
            'silent' => $options['silent'] ?? false,
            'vibrate' => $options['vibrate'] ?? [100, 50, 100],
            'data' => $options['data'] ?? [],
            'actions' => $options['actions'] ?? [],
        ];
    }
}

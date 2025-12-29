<?php

namespace Xentixar\FilamentPushNotifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $endpoint
 * @property string|null $public_key
 * @property string|null $auth_token
 * @property string $content_encoding
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('filament-push-notifications.receiver_model'));
    }

    /**
     * Get the subscription data formatted for web-push library.
     */
    public function getSubscriptionArray(): array
    {
        return [
            'endpoint' => $this->endpoint,
            'keys' => [
                'p256dh' => $this->public_key,
                'auth' => $this->auth_token,
            ],
            'contentEncoding' => $this->content_encoding,
        ];
    }

    /**
     * Check if the subscription is valid.
     */
    public function isValid(): bool
    {
        return !empty($this->endpoint) && !empty($this->public_key) && !empty($this->auth_token);
    }
}

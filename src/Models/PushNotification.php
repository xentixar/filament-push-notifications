<?php

namespace Xentixar\FilamentPushNotifications\Models;

use Xentixar\FilamentPushNotifications\Enums\PushNotificationType;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $message
 * @property PushNotificationType $type
 * @property array $receivers
 * @property Carbon $scheduled_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PushNotification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'receivers',
        'scheduled_at',
    ];

    protected $casts = [
        'receivers' => 'array',
        'type' => PushNotificationType::class,
    ];
}

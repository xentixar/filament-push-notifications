<?php

use Illuminate\Support\Facades\Route;
use Xentixar\FilamentPushNotifications\Controllers\PushSubscriptionController;

Route::middleware(['web', 'auth'])->prefix('push-notifications')->group(function () {
    Route::get('/vapid-public-key', [PushSubscriptionController::class, 'getPublicKey'])->name('push-notifications.vapid-key');
    Route::post('/subscribe', [PushSubscriptionController::class, 'subscribe'])->name('push-notifications.subscribe');
    Route::delete('/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('push-notifications.unsubscribe');
    Route::get('/status', [PushSubscriptionController::class, 'status'])->name('push-notifications.status');
});

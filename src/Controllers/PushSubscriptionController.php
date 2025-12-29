<?php

namespace Xentixar\FilamentPushNotifications\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Xentixar\FilamentPushNotifications\Models\PushSubscription;

class PushSubscriptionController extends Controller
{
    /**
     * Get the VAPID public key.
     */
    public function getPublicKey(): JsonResponse
    {
        $publicKey = config('filament-push-notifications.web_push.vapid_public_key');

        if (!$publicKey) {
            return response()->json([
                'error' => 'VAPID public key not configured. Run: php artisan push:generate-vapid-keys',
            ], 500);
        }

        return response()->json([
            'publicKey' => $publicKey,
        ]);
    }

    /**
     * Subscribe to push notifications.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid subscription data',
                'details' => $validator->errors(),
            ], 422);
        }

        try {
            $subscription = PushSubscription::updateOrCreate(
                [
                    'endpoint' => $request->input('endpoint'),
                ],
                [
                    'user_id' => auth()->id(),
                    'public_key' => $request->input('keys.p256dh'),
                    'auth_token' => $request->input('keys.auth'),
                    'content_encoding' => $request->input('contentEncoding', 'aesgcm'),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to push notifications',
                'subscription_id' => $subscription->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save subscription',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unsubscribe from push notifications.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid request data',
                'details' => $validator->errors(),
            ], 422);
        }

        try {
            $deleted = PushSubscription::where('endpoint', $request->input('endpoint'))
                ->where('user_id', auth()->id())
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully unsubscribed from push notifications',
                ]);
            }

            return response()->json([
                'error' => 'Subscription not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to unsubscribe',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's subscription status.
     */
    public function status(): JsonResponse
    {
        $subscriptionCount = PushSubscription::where('user_id', auth()->id())->count();

        return response()->json([
            'subscribed' => $subscriptionCount > 0,
            'subscription_count' => $subscriptionCount,
        ]);
    }
}

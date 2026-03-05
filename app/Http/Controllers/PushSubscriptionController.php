<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Store a push subscription.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint'  => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth'   => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->input('endpoint')],
            [
                'user_id'    => Auth::id(),
                'p256dh_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Remove a push subscription.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        PushSubscription::where('endpoint', $request->input('endpoint'))->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get VAPID public key for the frontend.
     */
    public function vapidPublicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => env('VAPID_PUBLIC_KEY'),
        ]);
    }
}

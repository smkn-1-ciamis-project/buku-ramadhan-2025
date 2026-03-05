<?php

namespace App\Services;

use App\Models\PushNotification;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    /**
     * Send push notification to subscribed devices.
     *
     * @param  string  $title
     * @param  string  $body
     * @param  string  $target  all|siswa|guru|kesiswaan
     * @param  string|null  $url
     * @param  string|null  $icon
     * @return array{sent: int, failed: int}
     */
    public static function send(string $title, string $body, string $target = 'all', ?string $url = null, ?string $icon = null): array
    {
        $auth = [
            'VAPID' => [
                'subject'    => config('webpush.vapid.subject', env('VAPID_SUBJECT', 'mailto:admin@smkn1ciamis.id')),
                'publicKey'  => config('webpush.vapid.public_key', env('VAPID_PUBLIC_KEY')),
                'privateKey' => config('webpush.vapid.private_key', env('VAPID_PRIVATE_KEY')),
            ],
        ];

        $webPush = new WebPush($auth);
        $webPush->setReuseVAPIDHeaders(true);
        $webPush->setAutomaticPadding(2820);

        // Build payload
        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'icon'  => $icon ?: '/img/icons/icon-192x192.png',
            'badge' => '/img/icons/icon-72x72.png',
            'url'   => $url ?: '/',
            'tag'   => 'calakan-' . time(),
        ]);

        // Get subscriptions based on target
        $query = PushSubscription::query();

        if ($target !== 'all') {
            // Join with users and role_users to filter by role
            $query->whereHas('user', function ($q) use ($target) {
                $q->whereHas('role_user', function ($rq) use ($target) {
                    $roleNames = match ($target) {
                        'siswa'     => ['siswa'],
                        'guru'      => ['guru'],
                        'kesiswaan' => ['kesiswaan', 'kepala sekolah'],
                        default     => [],
                    };
                    $rq->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(TRIM(name))'), $roleNames);
                });
            });
        }

        $subscriptions = $query->get();

        if ($subscriptions->isEmpty()) {
            // Save notification record even if no subscribers
            PushNotification::create([
                'title'        => $title,
                'body'         => $body,
                'icon'         => $icon,
                'url'          => $url,
                'target'       => $target,
                'sent_count'   => 0,
                'failed_count' => 0,
                'sent_by'      => Auth::id(),
            ]);

            return ['sent' => 0, 'failed' => 0];
        }

        // Queue all notifications
        foreach ($subscriptions as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'keys'     => [
                        'p256dh' => $sub->p256dh_key,
                        'auth'   => $sub->auth_token,
                    ],
                ]),
                $payload
            );
        }

        // Send all and collect results
        $sent = 0;
        $failed = 0;
        $expiredEndpoints = [];

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
            } else {
                $failed++;
                // Remove expired subscriptions (410 Gone or 404 Not Found)
                $statusCode = $report->getResponse()?->getStatusCode();
                if (in_array($statusCode, [404, 410])) {
                    $expiredEndpoints[] = $report->getEndpoint();
                }
                Log::warning('Push notification failed', [
                    'endpoint' => $report->getEndpoint(),
                    'reason'   => $report->getReason(),
                ]);
            }
        }

        // Clean up expired subscriptions
        if (!empty($expiredEndpoints)) {
            PushSubscription::whereIn('endpoint', $expiredEndpoints)->delete();
        }

        // Save notification record
        PushNotification::create([
            'title'        => $title,
            'body'         => $body,
            'icon'         => $icon,
            'url'          => $url,
            'target'       => $target,
            'sent_count'   => $sent,
            'failed_count' => $failed,
            'sent_by'      => Auth::id(),
        ]);

        return ['sent' => $sent, 'failed' => $failed];
    }

    /**
     * Send push notification for a scheduled record (no new PushNotification created).
     */
    public static function sendForScheduled(string $title, string $body, string $target = 'all', ?string $url = null, ?string $icon = null, ?string $sentBy = null): array
    {
        $auth = [
            'VAPID' => [
                'subject'    => config('webpush.vapid.subject', env('VAPID_SUBJECT', 'mailto:admin@smkn1ciamis.id')),
                'publicKey'  => config('webpush.vapid.public_key', env('VAPID_PUBLIC_KEY')),
                'privateKey' => config('webpush.vapid.private_key', env('VAPID_PRIVATE_KEY')),
            ],
        ];

        $webPush = new WebPush($auth);
        $webPush->setReuseVAPIDHeaders(true);
        $webPush->setAutomaticPadding(2820);

        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'icon'  => $icon ?: '/img/icons/icon-192x192.png',
            'badge' => '/img/icons/icon-72x72.png',
            'url'   => $url ?: '/',
            'tag'   => 'calakan-' . time(),
        ]);

        $query = PushSubscription::query();
        if ($target !== 'all') {
            $query->whereHas('user', function ($q) use ($target) {
                $q->whereHas('role_user', function ($rq) use ($target) {
                    $roleNames = match ($target) {
                        'siswa'     => ['siswa'],
                        'guru'      => ['guru'],
                        'kesiswaan' => ['kesiswaan', 'kepala sekolah'],
                        default     => [],
                    };
                    $rq->whereIn(DB::raw('LOWER(TRIM(name))'), $roleNames);
                });
            });
        }

        $subscriptions = $query->get();
        if ($subscriptions->isEmpty()) {
            return ['sent' => 0, 'failed' => 0];
        }

        foreach ($subscriptions as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'keys'     => [
                        'p256dh' => $sub->p256dh_key,
                        'auth'   => $sub->auth_token,
                    ],
                ]),
                $payload
            );
        }

        $sent = 0;
        $failed = 0;
        $expiredEndpoints = [];

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
            } else {
                $failed++;
                $statusCode = $report->getResponse()?->getStatusCode();
                if (in_array($statusCode, [404, 410])) {
                    $expiredEndpoints[] = $report->getEndpoint();
                }
                Log::warning('Scheduled push notification failed', [
                    'endpoint' => $report->getEndpoint(),
                    'reason'   => $report->getReason(),
                ]);
            }
        }

        if (!empty($expiredEndpoints)) {
            PushSubscription::whereIn('endpoint', $expiredEndpoints)->delete();
        }

        return ['sent' => $sent, 'failed' => $failed];
    }
}

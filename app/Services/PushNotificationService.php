<?php

namespace App\Services;

use App\Models\PushNotification;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use RuntimeException;
use Throwable;

class PushNotificationService
{
    private const SEND_BATCH_SIZE = 200;

    private static function buildWebPush(): WebPush
    {
        $subject = (string) config('webpush.vapid.subject', env('VAPID_SUBJECT', 'mailto:admin@smkn1ciamis.id'));
        $publicKey = (string) config('webpush.vapid.public_key', env('VAPID_PUBLIC_KEY'));
        $privateKey = (string) config('webpush.vapid.private_key', env('VAPID_PRIVATE_KEY'));

        if (blank($publicKey) || blank($privateKey)) {
            throw new RuntimeException('Konfigurasi VAPID belum lengkap. Isi VAPID_PUBLIC_KEY dan VAPID_PRIVATE_KEY di environment produksi.');
        }

        $auth = [
            'VAPID' => [
                'subject'    => $subject,
                'publicKey'  => $publicKey,
                'privateKey' => $privateKey,
            ],
        ];

        $webPush = new WebPush($auth);
        $webPush->setReuseVAPIDHeaders(true);
        $webPush->setAutomaticPadding(2820);

        return $webPush;
    }

    /**
     * @param  array<int, string>  $expiredEndpoints
     */
    private static function flushReports(WebPush $webPush, int &$sent, int &$failed, array &$expiredEndpoints, string $context): void
    {
        try {
            foreach ($webPush->flush() as $report) {
                if ($report->isSuccess()) {
                    $sent++;
                    continue;
                }

                $failed++;
                $statusCode = $report->getResponse()?->getStatusCode();
                if (in_array($statusCode, [404, 410], true)) {
                    $expiredEndpoints[] = $report->getEndpoint();
                }

                Log::warning($context, [
                    'endpoint' => $report->getEndpoint(),
                    'reason'   => $report->getReason(),
                    'status'   => $statusCode,
                ]);
            }
        } catch (Throwable $e) {
            $failed++;

            Log::error($context . ' (flush exception)', [
                'reason' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
        }
    }

    private static function applyTargetFilter($query, string $target): void
    {
        if (str_starts_with($target, 'kelas_multi:')) {
            $raw = trim(substr($target, strlen('kelas_multi:')));
            $kelasIds = array_values(array_unique(array_filter(array_map('trim', explode(',', $raw)))));

            if (empty($kelasIds)) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->whereHas('user', fn($q) => $q->whereIn('kelas_id', $kelasIds));
            return;
        }

        if (str_starts_with($target, 'kelas_')) {
            $kelasId = trim(substr($target, strlen('kelas_')));

            if ($kelasId === '') {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->whereHas('user', fn($q) => $q->where('kelas_id', $kelasId));
            return;
        }

        if ($target === 'all') {
            return;
        }

        $query->whereHas('user', function ($q) use ($target) {
            $q->whereHas('role_user', function ($rq) use ($target) {
                $roleNames = match ($target) {
                    'siswa'     => ['siswa'],
                    'guru'      => ['guru'],
                    'kesiswaan' => ['kesiswaan', 'kepala sekolah'],
                    default     => [],
                };

                if (empty($roleNames)) {
                    $rq->whereRaw('1 = 0');
                    return;
                }

                $rq->whereIn(DB::raw('LOWER(TRIM(name))'), $roleNames);
            });
        });
    }

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
        $webPush = self::buildWebPush();

        // Build payload
        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'icon'  => $icon ?: '/img/icons/icon-192x192.png',
            'badge' => '/img/icons/icon-72x72.png',
            'url'   => $url ?: '/',
            'tag'   => 'calakan-' . time(),
        ]);

        $query = PushSubscription::query();
        self::applyTargetFilter($query, $target);

        $totalSubscriptions = (clone $query)->count();

        if ($totalSubscriptions === 0) {
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

        // Send all and collect results
        $sent = 0;
        $failed = 0;
        $expiredEndpoints = [];

        $query->orderBy('created_at')
            ->chunk(self::SEND_BATCH_SIZE, function ($subscriptions) use ($webPush, $payload, &$sent, &$failed, &$expiredEndpoints) {
                foreach ($subscriptions as $sub) {
                    try {
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
                    } catch (Throwable $e) {
                        $failed++;
                        Log::warning('Push subscription skipped (invalid payload)', [
                            'subscription_id' => $sub->id,
                            'endpoint' => $sub->endpoint,
                            'reason' => $e->getMessage(),
                        ]);
                    }
                }

                self::flushReports($webPush, $sent, $failed, $expiredEndpoints, 'Push notification failed');
            });

        // Clean up expired subscriptions
        if (!empty($expiredEndpoints)) {
            PushSubscription::whereIn('endpoint', array_values(array_unique($expiredEndpoints)))->delete();
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
        $webPush = self::buildWebPush();

        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'icon'  => $icon ?: '/img/icons/icon-192x192.png',
            'badge' => '/img/icons/icon-72x72.png',
            'url'   => $url ?: '/',
            'tag'   => 'calakan-' . time(),
        ]);

        $query = PushSubscription::query();
        self::applyTargetFilter($query, $target);

        $totalSubscriptions = (clone $query)->count();
        if ($totalSubscriptions === 0) {
            return ['sent' => 0, 'failed' => 0];
        }

        $sent = 0;
        $failed = 0;
        $expiredEndpoints = [];

        $query->orderBy('created_at')
            ->chunk(self::SEND_BATCH_SIZE, function ($subscriptions) use ($webPush, $payload, &$sent, &$failed, &$expiredEndpoints) {
                foreach ($subscriptions as $sub) {
                    try {
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
                    } catch (Throwable $e) {
                        $failed++;
                        Log::warning('Scheduled push subscription skipped (invalid payload)', [
                            'subscription_id' => $sub->id,
                            'endpoint' => $sub->endpoint,
                            'reason' => $e->getMessage(),
                        ]);
                    }
                }

                self::flushReports($webPush, $sent, $failed, $expiredEndpoints, 'Scheduled push notification failed');
            });

        if (!empty($expiredEndpoints)) {
            PushSubscription::whereIn('endpoint', array_values(array_unique($expiredEndpoints)))->delete();
        }

        return ['sent' => $sent, 'failed' => $failed];
    }
}

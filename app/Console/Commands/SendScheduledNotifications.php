<?php

namespace App\Console\Commands;

use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledNotifications extends Command
{
    protected $signature = 'push:send-scheduled {--limit=100 : Batas jumlah notifikasi scheduled yang diproses per eksekusi}';
    protected $description = 'Kirim push notifikasi yang sudah dijadwalkan dan waktunya sudah tiba';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $notifications = PushNotification::scheduledReady()
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();

        Log::info('push:send-scheduled started', [
            'count' => $notifications->count(),
            'limit' => $limit,
            'now' => now()->toDateTimeString(),
        ]);

        if ($notifications->isEmpty()) {
            $this->info('Tidak ada notifikasi terjadwal yang perlu dikirim.');
            return self::SUCCESS;
        }

        foreach ($notifications as $notif) {
            $this->info("Mengirim: {$notif->title} → {$notif->target}");

            try {
                $result = PushNotificationService::sendForScheduled(
                    $notif->title,
                    $notif->body,
                    $notif->target,
                    $notif->url,
                    null,
                    $notif->sent_by
                );

                $status = $this->resolveStatusFromResult($result['sent'] ?? 0, $result['failed'] ?? 0);

                $notif->update([
                    'status'       => $status,
                    'sent_count'   => $result['sent'],
                    'failed_count' => $result['failed'],
                ]);

                $this->info("  ✓ Status: {$status} | Terkirim: {$result['sent']}, Gagal: {$result['failed']}");

                Log::info('Scheduled push notification processed', [
                    'id' => $notif->id,
                    'target' => $notif->target,
                    'status' => $status,
                    'sent' => $result['sent'],
                    'failed' => $result['failed'],
                    'scheduled_at' => optional($notif->scheduled_at)?->toDateTimeString(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Scheduled push notification failed', [
                    'id'    => $notif->id,
                    'target' => $notif->target,
                    'error' => $e->getMessage(),
                ]);

                $notif->update([
                    'status' => 'failed',
                    'failed_count' => max(1, (int) $notif->failed_count),
                ]);
                $this->error("  ✗ Gagal: {$e->getMessage()}");
            }
        }

        $this->info("Selesai. {$notifications->count()} notifikasi diproses.");

        return self::SUCCESS;
    }

    private function resolveStatusFromResult(int $sent, int $failed): string
    {
        if ($sent > 0 && $failed === 0) {
            return 'sent';
        }

        if ($sent > 0 && $failed > 0) {
            return 'partial';
        }

        if ($sent === 0 && $failed > 0) {
            return 'failed';
        }

        return 'no_subscribers';
    }
}

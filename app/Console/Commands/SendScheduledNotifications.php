<?php

namespace App\Console\Commands;

use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledNotifications extends Command
{
    protected $signature = 'push:send-scheduled';
    protected $description = 'Kirim push notifikasi yang sudah dijadwalkan dan waktunya sudah tiba';

    public function handle(): int
    {
        $notifications = PushNotification::scheduledReady()->get();

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

                $notif->update([
                    'status'       => 'sent',
                    'sent_count'   => $result['sent'],
                    'failed_count' => $result['failed'],
                ]);

                $this->info("  ✓ Terkirim: {$result['sent']}, Gagal: {$result['failed']}");
            } catch (\Throwable $e) {
                Log::error('Scheduled push notification failed', [
                    'id'    => $notif->id,
                    'error' => $e->getMessage(),
                ]);

                $notif->update(['status' => 'failed']);
                $this->error("  ✗ Gagal: {$e->getMessage()}");
            }
        }

        $this->info("Selesai. {$notifications->count()} notifikasi diproses.");

        return self::SUCCESS;
    }
}

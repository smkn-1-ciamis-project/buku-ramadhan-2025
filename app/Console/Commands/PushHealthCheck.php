<?php

namespace App\Console\Commands;

use App\Models\PushNotification;
use App\Models\PushSubscription;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class PushHealthCheck extends Command
{
  protected $signature = 'push:health-check
        {--simulate-send : Jalankan uji kirim notifikasi test}
        {--cleanup-invalid : Hapus data subscription yang jelas invalid}';

  protected $description = 'Diagnosa kesiapan push notification (config, dependency, data subscription, dan uji kirim opsional)';

  public function handle(): int
  {
    $warnings = 0;
    $failures = 0;

    $this->line('');
    $this->info('=== PUSH HEALTH CHECK ===');

    $this->line('');
    $this->info('[1/5] Dependency');
    $webPushOk = class_exists(WebPush::class);
    $subscriptionOk = class_exists(Subscription::class);

    if ($webPushOk && $subscriptionOk) {
      $this->line('  PASS  Minishlink WebPush terdeteksi.');
    } else {
      $failures++;
      $this->error('  FAIL  Package web-push belum siap. Jalankan: composer install');
    }

    $this->line('');
    $this->info('[2/5] Konfigurasi VAPID');
    $subject = (string) config('webpush.vapid.subject');
    $publicKey = (string) config('webpush.vapid.public_key');
    $privateKey = (string) config('webpush.vapid.private_key');

    $subjectOk = $subject !== '';
    $publicOk = $publicKey !== '';
    $privateOk = $privateKey !== '';

    if (! $subjectOk || ! $publicOk || ! $privateOk) {
      $failures++;
      $this->error('  FAIL  VAPID belum lengkap (subject/public/private).');
    } else {
      $this->line('  PASS  VAPID subject/public/private terisi.');

      if (! str_starts_with($subject, 'mailto:')) {
        $warnings++;
        $this->warn('  WARN  VAPID_SUBJECT disarankan format mailto:contoh@domain.com');
      }

      if (strlen($publicKey) < 80 || strlen($privateKey) < 40) {
        $warnings++;
        $this->warn('  WARN  Panjang VAPID key terlihat tidak normal.');
      }
    }

    $this->line('');
    $this->info('[3/5] Struktur Database');
    $subsTable = Schema::hasTable('push_subscriptions');
    $notifTable = Schema::hasTable('push_notifications');

    if ($subsTable && $notifTable) {
      $this->line('  PASS  Tabel push_subscriptions dan push_notifications tersedia.');
    } else {
      $failures++;
      $this->error('  FAIL  Tabel push belum lengkap. Jalankan migrasi.');
      $this->line('        php artisan migrate --force');
    }

    if (! $subsTable || ! $notifTable) {
      return self::FAILURE;
    }

    $this->line('');
    $this->info('[4/5] Kualitas Data Subscription');

    $total = PushSubscription::count();
    $endpointEmpty = PushSubscription::whereNull('endpoint')->orWhere('endpoint', '')->count();
    $p256Empty = PushSubscription::whereNull('p256dh_key')->orWhere('p256dh_key', '')->count();
    $authEmpty = PushSubscription::whereNull('auth_token')->orWhere('auth_token', '')->count();
    $endpointNotHttps = PushSubscription::whereRaw("endpoint IS NOT NULL AND endpoint != '' AND endpoint NOT LIKE 'https://%'")->count();
    $p256Short = PushSubscription::whereRaw('CHAR_LENGTH(p256dh_key) < 80')->count();
    $authShort = PushSubscription::whereRaw('CHAR_LENGTH(auth_token) < 16')->count();
    $orphanUser = PushSubscription::whereNull('user_id')->count();

    $this->line("  INFO  Total subscription: {$total}");
    $this->line("  INFO  endpoint kosong: {$endpointEmpty}");
    $this->line("  INFO  p256 kosong: {$p256Empty}");
    $this->line("  INFO  auth kosong: {$authEmpty}");
    $this->line("  INFO  endpoint non-https: {$endpointNotHttps}");
    $this->line("  INFO  p256 terlalu pendek: {$p256Short}");
    $this->line("  INFO  auth terlalu pendek: {$authShort}");
    $this->line("  INFO  user_id null: {$orphanUser}");

    $invalidCount = $endpointEmpty + $p256Empty + $authEmpty + $endpointNotHttps;

    if ($invalidCount > 0) {
      $warnings++;
      $this->warn("  WARN  Ditemukan {$invalidCount} indikasi data invalid.");
    } else {
      $this->line('  PASS  Tidak ditemukan data invalid yang jelas.');
    }

    if ($this->option('cleanup-invalid')) {
      $deleted = PushSubscription::query()
        ->where(function ($q) {
          $q->whereNull('endpoint')
            ->orWhere('endpoint', '')
            ->orWhereNull('p256dh_key')
            ->orWhere('p256dh_key', '')
            ->orWhereNull('auth_token')
            ->orWhere('auth_token', '')
            ->orWhereRaw("endpoint NOT LIKE 'https://%'");
        })
        ->delete();

      $this->line("  INFO  Cleanup invalid aktif, data terhapus: {$deleted}");
    }

    $this->line('');
    $this->info('[5/5] Status Notifikasi & Uji Kirim');
    $scheduled = PushNotification::where('status', 'scheduled')->count();
    $failedNotif = PushNotification::where('status', 'failed')->count();

    $this->line("  INFO  Notifikasi scheduled: {$scheduled}");
    $this->line("  INFO  Notifikasi failed: {$failedNotif}");

    if ($this->option('simulate-send')) {
      try {
        $result = PushNotificationService::send(
          '[Health Check] Push Test',
          'Tes health-check dari server. Jika ini muncul, pipeline push sudah berjalan.',
          'all',
          '/'
        );

        $this->line("  PASS  Simulasi kirim selesai. sent={$result['sent']} failed={$result['failed']}");
      } catch (Throwable $e) {
        $failures++;
        $this->error('  FAIL  Simulasi kirim gagal: ' . $e->getMessage());
      }
    } else {
      $this->line('  INFO  Simulasi kirim dilewati (pakai --simulate-send untuk mengetes).');
    }

    $this->line('');
    $this->info('=== RINGKASAN ===');
    $this->line("Warnings : {$warnings}");
    $this->line("Failures : {$failures}");

    if ($failures > 0) {
      $this->error('Status akhir: TIDAK SIAP');
      return self::FAILURE;
    }

    if ($warnings > 0) {
      $this->warn('Status akhir: SIAP DENGAN CATATAN');
      return self::SUCCESS;
    }

    $this->info('Status akhir: SIAP');
    return self::SUCCESS;
  }
}

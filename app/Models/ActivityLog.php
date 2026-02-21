<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
  use UuidTrait;

  protected $fillable = [
    'user_id',
    'activity',
    'role',
    'panel',
    'ip_address',
    'user_agent',
    'location',
    'metadata',
  ];

  protected $casts = [
    'metadata' => 'array',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Log an activity.
   */
  public static function log(string $activity, ?User $user = null, array $extra = []): self
  {
    $request = request();
    $ip = $request->ip();

    // Detect panel from URL
    $panel = null;
    $path = $request->path();
    if (str_contains($path, 'portal-admin-smkn1')) {
      $panel = 'superadmin';
    } elseif (str_contains($path, 'portal-guru-smkn1')) {
      $panel = 'guru';
    } elseif (str_contains($path, 'portal-kesiswaan-smkn1')) {
      $panel = 'kesiswaan';
    } elseif (str_contains($path, 'siswa')) {
      $panel = 'siswa';
    }

    // Get location from IP (simple approach using ip-api.com for non-local IPs)
    $location = self::getLocationFromIp($ip);

    return self::create([
      'user_id' => $user?->id,
      'activity' => $activity,
      'role' => $user?->role_user?->name,
      'panel' => $panel ?? ($extra['panel'] ?? null),
      'ip_address' => $ip,
      'user_agent' => $request->userAgent(),
      'location' => $location,
      'metadata' => !empty($extra) ? $extra : null,
    ]);
  }

  /**
   * Get location string from IP address.
   */
  protected static function getLocationFromIp(?string $ip): ?string
  {
    if (!$ip || in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
      return 'Localhost';
    }

    // Private/reserved IPs
    if (
      str_starts_with($ip, '10.') ||
      str_starts_with($ip, '172.') ||
      str_starts_with($ip, '192.168.')
    ) {
      return 'Jaringan Lokal';
    }

    try {
      $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,city,regionName,country&lang=id", false, stream_context_create([
        'http' => ['timeout' => 2],
      ]));

      if ($response) {
        $data = json_decode($response, true);
        if (($data['status'] ?? '') === 'success') {
          return trim(($data['city'] ?? '') . ', ' . ($data['regionName'] ?? '') . ', ' . ($data['country'] ?? ''), ', ');
        }
      }
    } catch (\Throwable $e) {
      // Silently fail
    }

    return null;
  }

  /**
   * Get browser name from user agent.
   */
  public function getBrowserAttribute(): string
  {
    $ua = $this->user_agent ?? '';
    if (str_contains($ua, 'Edg/')) return 'Edge';
    if (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera')) return 'Opera';
    if (str_contains($ua, 'Brave')) return 'Brave';
    if (str_contains($ua, 'Chrome/')) return 'Chrome';
    if (str_contains($ua, 'Firefox/')) return 'Firefox';
    if (str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome')) return 'Safari';
    return 'Lainnya';
  }

  /**
   * Get device/OS from user agent.
   */
  public function getDeviceAttribute(): string
  {
    $ua = $this->user_agent ?? '';
    if (str_contains($ua, 'Windows')) return 'Windows';
    if (str_contains($ua, 'Macintosh')) return 'macOS';
    if (str_contains($ua, 'Linux') && !str_contains($ua, 'Android')) return 'Linux';
    if (str_contains($ua, 'Android')) return 'Android';
    if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
    return 'Lainnya';
  }
}

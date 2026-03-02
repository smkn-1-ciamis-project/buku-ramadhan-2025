<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
  use UuidTrait;

  protected $fillable = [
    'group',
    'key',
    'value',
    'type',
    'label',
    'description',
  ];

  /* ──────────────────────────────── Cache TTL (1 hour) ───────── */
  private const CACHE_TTL = 3600;
  private const CACHE_PREFIX = 'app_setting_';
  private const CACHE_ALL_KEY = 'app_settings_all';

    /* ──────────────────────────────── Accessors ────────────────── */

  /**
   * Get a setting value by key, with caching.
   */
  public static function getValue(string $key, mixed $default = null): mixed
  {
    $setting = Cache::remember(self::CACHE_PREFIX . $key, self::CACHE_TTL, function () use ($key) {
      return static::where('key', $key)->first();
    });

    if (!$setting) {
      return $default;
    }

    return self::castValue($setting->value, $setting->type);
  }

  /**
   * Set a setting value by key.
   */
  public static function setValue(string $key, mixed $value): void
  {
    $setting = static::where('key', $key)->first();

    if ($setting) {
      $setting->update(['value' => is_array($value) ? json_encode($value) : (string) $value]);
    }
  }

  /**
   * Get all settings for a group, with caching.
   */
  public static function getGroup(string $group): array
  {
    $settings = Cache::remember(self::CACHE_PREFIX . 'group_' . $group, self::CACHE_TTL, function () use ($group) {
      return static::where('group', $group)->get();
    });

    $result = [];
    foreach ($settings as $setting) {
      $result[$setting->key] = self::castValue($setting->value, $setting->type);
    }

    return $result;
  }

  /**
   * Get all settings as key-value map (cached).
   */
  public static function getAllCached(): array
  {
    return Cache::remember(self::CACHE_ALL_KEY, self::CACHE_TTL, function () {
      $settings = static::all();
      $result = [];
      foreach ($settings as $setting) {
        $result[$setting->key] = self::castValue($setting->value, $setting->type);
      }
      return $result;
    });
  }

  /**
   * Get settings formatted for the frontend JS.
   *   Returns a clean object with only the keys JS needs.
   */
  public static function getForFrontend(): array
  {
    $all = self::getAllCached();

    return [
      'ramadhan_start_date' => $all['ramadhan_start_date'] ?? '2026-02-19',
      'ramadhan_end_date'   => $all['ramadhan_end_date'] ?? '2026-03-20',
      'ramadhan_total_days' => (int) ($all['ramadhan_total_days'] ?? 30),
      'hijri_year'          => $all['hijri_year'] ?? '1447 H',
      'prayer_api_url'      => $all['prayer_api_url'] ?? 'https://api.aladhan.com/v1/timings/',
      'prayer_api_method'   => (int) ($all['prayer_api_method'] ?? 20),
      'quran_api_url'       => $all['quran_api_url'] ?? 'https://api.alquran.cloud/v1/ayah/',
      'nominatim_api_url'   => $all['nominatim_api_url'] ?? 'https://nominatim.openstreetmap.org/reverse',
      'default_latitude'    => (float) ($all['default_latitude'] ?? -7.3305),
      'default_longitude'   => (float) ($all['default_longitude'] ?? 108.3508),
      'default_city'        => $all['default_city'] ?? 'Ciamis',
    ];
  }

  /* ──────────────────────────────── Type Casting ─────────────── */

  private static function castValue(?string $value, string $type): mixed
  {
    if ($value === null) {
      return null;
    }

    return match ($type) {
      'integer' => (int) $value,
      'float'   => (float) $value,
      'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
      'json'    => json_decode($value, true),
      default   => $value,
    };
  }

  /* ──────────────────────────────── Cache Invalidation ───────── */

  protected static function booted(): void
  {
    $clearCache = function (self $model) {
      Cache::forget(self::CACHE_PREFIX . $model->key);
      Cache::forget(self::CACHE_PREFIX . 'group_' . $model->group);
      Cache::forget(self::CACHE_ALL_KEY);
    };

    static::saved($clearCache);
    static::deleted($clearCache);
  }
}

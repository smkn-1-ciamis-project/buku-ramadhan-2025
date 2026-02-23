<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FormSetting extends Model
{
  use UuidTrait;

  protected $fillable = [
    'agama',
    'sections',
    'is_active',
  ];

  protected $casts = [
    'sections' => 'array',
    'is_active' => 'boolean',
  ];

  /**
   * Get form setting for a specific religion, with caching (60 min TTL).
   */
  public static function getForAgama(string $agama): ?self
  {
    return Cache::remember("form_setting_{$agama}", 3600, function () use ($agama) {
      return static::where('agama', $agama)->where('is_active', true)->first();
    });
  }

  /**
   * Clear cache when model is saved/deleted.
   */
  protected static function booted(): void
  {
    static::saved(fn(self $m) => Cache::forget("form_setting_{$m->agama}"));
    static::deleted(fn(self $m) => Cache::forget("form_setting_{$m->agama}"));
  }
}

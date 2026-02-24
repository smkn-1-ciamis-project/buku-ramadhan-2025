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
   * Normalizes agama variant spellings (e.g. "Budha" → "Buddha").
   */
  public static function getForAgama(string $agama): ?self
  {
    $normalized = \App\Models\User::normalizeAgama($agama) ?? $agama;

    return Cache::remember("form_setting_{$normalized}", 3600, function () use ($normalized) {
      return static::where('agama', $normalized)->first();
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

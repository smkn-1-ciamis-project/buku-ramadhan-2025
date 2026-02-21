<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

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
   * Get form setting for a specific religion, with caching.
   */
  public static function getForAgama(string $agama): ?self
  {
    return static::where('agama', $agama)->where('is_active', true)->first();
  }
}

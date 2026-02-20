<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
  use HasFactory, UuidTrait;

  protected $fillable = [
    'user_id',
    'hari_ke',
    'data',
  ];

  protected $casts = [
    'data' => 'array',
    'hari_ke' => 'integer',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}

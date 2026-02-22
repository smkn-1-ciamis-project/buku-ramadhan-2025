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
    'status',
    'verified_by',
    'verified_at',
    'catatan_guru',
    'kesiswaan_status',
    'validated_by',
    'validated_at',
    'catatan_kesiswaan',
  ];

  protected $casts = [
    'data' => 'array',
    'hari_ke' => 'integer',
    'verified_at' => 'datetime',
    'validated_at' => 'datetime',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function verifier(): BelongsTo
  {
    return $this->belongsTo(User::class, 'verified_by');
  }

  public function validator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'validated_by');
  }
}

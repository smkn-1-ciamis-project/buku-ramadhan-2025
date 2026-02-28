<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Kelas extends Model
{
  use HasFactory, UuidTrait;

  protected $table = 'kelas';

  protected $fillable = [
    'nama',
    'wali_id',
  ];

  /**
   * Guru wali kelas.
   */
  public function wali(): BelongsTo
  {
    return $this->belongsTo(User::class, 'wali_id');
  }

  /**
   * Siswa dalam kelas ini.
   */
  public function siswa(): HasMany
  {
    return $this->hasMany(User::class, 'kelas_id');
  }

  /**
   * All form submissions from siswa in this kelas (via users).
   */
  public function formSubmissions(): HasManyThrough
  {
    return $this->hasManyThrough(FormSubmission::class, User::class, 'kelas_id', 'user_id');
  }
}

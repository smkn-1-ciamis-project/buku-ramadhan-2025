<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerCheckin extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'prayer_checkins';

    protected $fillable = [
        'user_id',
        'tanggal',
        'shalat',
        'tipe',
        'status',
        'waktu_checkin',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_checkin' => 'datetime',
    ];

    /**
     * Daftar shalat wajib (5 waktu + tarawih)
     */
    public const SHALAT_WAJIB = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya', 'tarawih'];

    /**
     * Daftar shalat sunnah
     */
    public const SHALAT_SUNNAH = ['rowatib', 'tahajud', 'dhuha'];

    /**
     * Status check-in yang valid untuk shalat wajib
     */
    public const STATUS_WAJIB = ['jamaah', 'munfarid', 'tidak'];

    /**
     * Status check-in yang valid untuk shalat sunnah
     */
    public const STATUS_SUNNAH = ['ya', 'tidak'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ambil semua check-in hari ini untuk user tertentu.
     */
    public static function todayForUser(string $userId): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('user_id', $userId)
            ->where('tanggal', now()->toDateString())
            ->get();
    }

    /**
     * Ambil check-in untuk tanggal tertentu.
     */
    public static function forDate(string $userId, string $date): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('user_id', $userId)
            ->where('tanggal', $date)
            ->get();
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\UuidTrait;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, UuidTrait;

    /**
     * Always eager-load role_user to avoid N+1 on canAccessPanel().
     */
    protected $with = ['role_user'];

    public function canAccessPanel(Panel $panel): bool
    {
        $roleName = strtolower(trim($this->role_user?->name ?? ''));

        if ($roleName === '') {
            return false;
        }

        // Superadmin bisa akses semua panel
        if (in_array($roleName, ['super admin', 'superadmin'])) {
            return true;
        }

        // Role lain hanya bisa akses panel sendiri
        return match ($panel->getId()) {
            'siswa'      => $roleName === 'siswa',
            'guru'       => $roleName === 'guru',
            'kesiswaan'  => in_array($roleName, ['kesiswaan', 'kepala sekolah']),
            default      => false,
        };
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'nisn',
        'agama',
        'no_hp',
        'password',
        'must_change_password',
        'role_user_id',
        'kelas_id',
        'jenis_kelamin',
        'active_session_id',
        'session_login_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'must_change_password' => 'boolean',
        'session_login_at' => 'datetime',
    ];


    public function role_user(): BelongsTo
    {
        return $this->belongsTo(RoleUser::class);
    }

    /**
     * Kelas tempat siswa ini terdaftar.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Kelas-kelas yang diwalikan guru ini.
     */
    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_id');
    }

    /**
     * Formulir yang disubmit siswa ini.
     */
    public function formSubmissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class, 'user_id');
    }

    /* ------------------------------------------------------------------ */
    /*  Agama helpers                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Canonical religion values stored in the database.
     */
    public const VALID_AGAMA = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];

    /**
     * Common alternate spellings → canonical mapping.
     */
    private const AGAMA_ALIASES = [
        'budha'     => 'Buddha',
        'budhha'    => 'Buddha',
        'khonghucu' => 'Konghucu',
        'kong hu cu' => 'Konghucu',
    ];

    /**
     * Non-Muslim religion values (lowercase) for isMuslim checks.
     */
    private const NON_MUSLIM = [
        'kristen',
        'katolik',
        'hindu',
        'buddha',
        'budha',
        'konghucu',
        'khonghucu',
    ];

    /**
     * Normalize an agama string to its canonical form.
     * Returns null if the value doesn't match any known religion.
     *
     * Example: "budha" → "Buddha", "ISLAM" → "Islam", "foo" → null
     */
    public static function normalizeAgama(?string $agama): ?string
    {
        if ($agama === null || trim($agama) === '') {
            return null;
        }

        $lower = strtolower(trim($agama));

        // Check aliases first
        if (isset(self::AGAMA_ALIASES[$lower])) {
            return self::AGAMA_ALIASES[$lower];
        }

        // Case-insensitive match against canonical list
        foreach (self::VALID_AGAMA as $canonical) {
            if (strtolower($canonical) === $lower) {
                return $canonical;
            }
        }

        return null;
    }

    /**
     * Check whether a given agama string is Muslim (Islam).
     * Null / empty / unknown defaults to true (treated as Muslim).
     */
    public static function isMuslimAgama(?string $agama): bool
    {
        return !in_array(strtolower($agama ?? ''), self::NON_MUSLIM);
    }
}

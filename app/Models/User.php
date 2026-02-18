<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\UuidTrait;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, UuidTrait;

    public function canAccessPanel(Panel $panel): bool
    {
        // Get role name
        $roleName = $this->role_user?->name;

        if (!$roleName) {
            return false;
        }

        // Panel access based on role
        return match ($panel->getId()) {
            'siswa' => str_contains(strtolower($roleName), 'siswa'),
            'guru' => str_contains(strtolower($roleName), 'guru'),
            'superadmin' => str_contains(strtolower($roleName), 'super admin') || str_contains(strtolower($roleName), 'superadmin'),
            'kesiswaan' => str_contains(strtolower($roleName), 'kesiswaan') || str_contains(strtolower($roleName), 'kepala sekolah'),
            'admin' => str_contains(strtolower($roleName), 'admin'),
            default => false,
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
        'password',
        'role_user_id',
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
    ];


    public function role_user(): BelongsTo
    {
        return $this->belongsTo(RoleUser::class);
    }
}

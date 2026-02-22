<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class RoleUser extends Model
{
    use HasFactory, UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'need_approval',
        'author_id',
        'menu_visibility',
    ];

    protected $casts = [
        'menu_visibility' => 'array',
    ];

    public const NEED_APPROVAL = true;
    public const NOT_NEED_APPROVAL = false;
    public const APPROVE_STATUS = [
        self::NEED_APPROVAL => 'Need Approve',
        self::NOT_NEED_APPROVAL => 'No Need Approve'
    ];


    // public function user() : HasMany
    // {
    //     return $this->hasMany(User::class);
    // }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_user_id');
    }

    /**
     * Check if a menu key is visible for this role.
     * Returns true if menu_visibility is null (all visible by default).
     */
    public function isMenuVisible(string $key): bool
    {
        if (empty($this->menu_visibility)) {
            return true;
        }
        return (bool) ($this->menu_visibility[$key] ?? true);
    }

    /**
     * Helper to check nav visibility from the currently authenticated user's role.
     */
    public static function checkNav(string $key): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $role = $user?->role_user;
        if (!$role) return true;
        return $role->isMenuVisible($key);
    }
}

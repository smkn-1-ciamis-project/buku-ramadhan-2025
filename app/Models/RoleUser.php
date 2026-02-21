<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}

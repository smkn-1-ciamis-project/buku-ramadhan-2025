<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatHistory extends Model
{
    protected $fillable = ['user_id', 'role', 'content', 'religion', 'is_cached'];

    protected function casts(): array
    {
        return [
            'is_cached' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

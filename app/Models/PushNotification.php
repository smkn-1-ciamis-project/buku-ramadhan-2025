<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushNotification extends Model
{
    use UuidTrait;

    protected $fillable = [
        'title',
        'body',
        'icon',
        'url',
        'target',
        'scheduled_at',
        'sent_count',
        'failed_count',
        'status',
        'sent_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function scopeScheduledReady($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now());
    }
}

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
        'sent_count',
        'failed_count',
        'sent_by',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}

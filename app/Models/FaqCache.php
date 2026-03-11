<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqCache extends Model
{
    protected $fillable = ['religion', 'question_hash', 'question', 'answer', 'hit_count'];
}

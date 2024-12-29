<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotQuizState extends Model
{
    protected $fillable = [
        'bot_id',
        'chat_id',
        'current_step'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotGreeting extends Model
{
    protected $fillable = [
        'bot_id',
        'template',
        'delay',
        'schedule_from',
        'schedule_to',
    ];
}

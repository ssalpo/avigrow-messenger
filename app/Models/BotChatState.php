<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotChatState extends Model
{
    protected $fillable = [
        'bot_id',
        'account_id',
        'chat_id',
        'greeted'
    ];
}

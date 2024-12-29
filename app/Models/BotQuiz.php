<?php

namespace App\Models;

use App\Enums\BotQuizAnswerTypes;
use Illuminate\Database\Eloquent\Model;

class BotQuiz extends Model
{
    protected $fillable = [
        'bot_id',
        'name',
        'content',
        'answer_type',
        'options'
    ];

    protected $casts = [
        'answer_type' => BotQuizAnswerTypes::class,
        'options' => 'array'
    ];
}

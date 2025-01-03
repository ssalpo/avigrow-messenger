<?php

namespace App\Models;

use App\Enums\BotTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bot extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_active',
        'mark_chat_as_unread'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'type' => BotTypes::class,
        'mark_chat_as_unread' => 'boolean',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function greetings(): HasMany
    {
        return $this->hasMany(BotGreeting::class);
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(BotTrigger::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(BotQuiz::class);
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BotSchedule::class);
    }
}

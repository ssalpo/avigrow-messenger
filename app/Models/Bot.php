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
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'type' => BotTypes::class
    ];

    public function greetings(): HasMany
    {
        return $this->hasMany(BotGreeting::class);
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(BotTrigger::class);
    }
}

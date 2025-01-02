<?php

namespace App\Models;

use App\Enums\WeekDays;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BotSchedule extends Model
{
    protected $fillable = [
        'bot_id',
        'day_of_week',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => WeekDays::class,
    ];

    public function scopeActive(Builder $q): void
    {
        $q->where('is_active', true);
    }

    public function scopeCurrentWeekDay(Builder $q): void
    {
        $q->where('day_of_week', now()->format('w'));
    }

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(BotScheduleSlot::class);
    }
}

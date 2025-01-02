<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotScheduleSlot extends Model
{
    protected $fillable = [
        'bot_schedule_id',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function botSchedule(): BelongsTo
    {
        return $this->belongsTo(BotSchedule::class);
    }

    public function scopeActive(Builder $q): void
    {
        $q->where('is_active', true);
    }
}

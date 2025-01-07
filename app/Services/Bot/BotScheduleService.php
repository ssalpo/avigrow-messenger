<?php

namespace App\Services\Bot;

use App\Models\BotSchedule;

class BotScheduleService
{
    public static function isInSchedule(int $botId): bool
    {
        $currentTime = now()->format('H:i:s');

        $schedule = BotSchedule::where('bot_id', $botId)
            ->currentWeekDay()
            ->active()
            ->with(['slots' => fn($q) => $q->active()])
            ->first();

        if (!$schedule || $schedule->slots->count() === 0) {
            return true;
        }

        foreach ($schedule->slots as $slot) {
            if ($slot->start_time <= $currentTime && $slot->end_time >= $currentTime) {
                return true;
            }
        }

        return false;
    }
}

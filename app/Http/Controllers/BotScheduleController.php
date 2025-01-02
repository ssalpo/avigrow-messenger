<?php

namespace App\Http\Controllers;

use App\Enums\WeekDays;
use App\Models\BotSchedule;
use Illuminate\Http\Request;

class BotScheduleController extends Controller
{
    public function toggleStatus(int $botId, int $dayOfWeek): void
    {
        if(!WeekDays::isValidDay($dayOfWeek)) {
            return;
        }

        $schedule = BotSchedule::firstOrCreate(['bot_id' => $botId, 'day_of_week' => $dayOfWeek]);

        $schedule->update(['is_active' => !$schedule->is_active]);
    }
}

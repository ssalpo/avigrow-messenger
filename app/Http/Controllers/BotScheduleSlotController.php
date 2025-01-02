<?php

namespace App\Http\Controllers;

use App\Enums\WeekDays;
use App\Http\Requests\BotScheduleSlotRequest;
use App\Models\BotSchedule;
use App\Models\BotScheduleSlot;
use Illuminate\Http\RedirectResponse;

class BotScheduleSlotController extends Controller
{
    public function store(int $botId, int $dayOfWeek, BotScheduleSlotRequest $request): RedirectResponse
    {
        if(WeekDays::isValidDay($dayOfWeek)) {
            $schedule = BotSchedule::firstOrCreate(['bot_id' => $botId, 'day_of_week' => $dayOfWeek]);

            $schedule->slots()->create($request->validated());
        }

        return redirect()->back();
    }

    public function update(int $botId, int $dayOfWeek, int $slotId, BotScheduleSlotRequest $request): RedirectResponse
    {
        $slot = BotScheduleSlot::whereHas('botSchedule', function ($query) use ($dayOfWeek, $botId) {
            $query->where(['bot_id' => $botId, 'day_of_week' => $dayOfWeek]);
        })->findOrFail($slotId);

        $slot->update($request->validated());

        return redirect()->back();
    }

    public function destroy(int $botId, int $dayOfWeek, int $slotId): RedirectResponse
    {
        $slot = BotScheduleSlot::whereHas('botSchedule', function ($query) use ($dayOfWeek, $botId) {
            $query->where(['bot_id' => $botId, 'day_of_week' => $dayOfWeek]);
        })->findOrFail($slotId);

        $slot->delete();

        return redirect()->back();
    }
}

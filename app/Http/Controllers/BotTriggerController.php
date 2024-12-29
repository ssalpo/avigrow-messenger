<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bots\BotTriggerRequest;
use App\Models\BotTrigger;
use Illuminate\Http\RedirectResponse;

class BotTriggerController extends Controller
{
    public function store(int $botId, BotTriggerRequest $request): RedirectResponse
    {
        BotTrigger::create($request->validated() + ['bot_id' => $botId]);

        return redirect()->back();
    }

    public function update(int $botId, int $botTrigger, BotTriggerRequest $request): RedirectResponse
    {
        BotTrigger::where(['bot_id' => $botId])
            ->findOrFail($botTrigger)
            ->update($request->validated());

        return redirect()->back();
    }

    public function destroy(int $botId, int $botTrigger): RedirectResponse
    {
        BotTrigger::where(['bot_id' => $botId])
            ->findOrFail($botTrigger)
            ->delete();

        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\BotGreetingRequest;
use App\Models\BotGreeting;
use Illuminate\Http\RedirectResponse;

class BotGreetingController extends Controller
{
    public function store(int $botId, BotGreetingRequest $request): RedirectResponse
    {
        BotGreeting::create($request->validated() + ['bot_id' => $botId]);

        return redirect()->back();
    }

    public function update(int $botId, int $id, BotGreetingRequest $request): RedirectResponse
    {
        BotGreeting::where(['bot_id' => $botId])
            ->findOrFail($id)
            ->update($request->validated());

        return redirect()->back();
    }

    public function destroy(int $botId, int $botTrigger): RedirectResponse
    {
        BotGreeting::where(['bot_id' => $botId])
            ->findOrFail($botTrigger)
            ->delete();

        return redirect()->back();
    }
}

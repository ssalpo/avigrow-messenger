<?php

namespace App\Http\Controllers;

use App\Http\Requests\BotQuizResortRequest;
use App\Http\Requests\Bots\BotQuizRequest;
use App\Models\BotQuiz;
use Illuminate\Http\RedirectResponse;

class BotQuizController extends Controller
{
    public function store(int $botId, BotQuizRequest $request): RedirectResponse
    {
        BotQuiz::create($request->validated() + ['bot_id' => $botId]);

        return redirect()->back();
    }

    public function update(int $botId, int $botQuiz, BotQuizRequest $request): RedirectResponse
    {
        BotQuiz::where(['bot_id' => $botId])
            ->findOrFail($botQuiz)
            ->update($request->validated());

        return redirect()->back();
    }

    public function destroy(int $botId, int $botQuiz): RedirectResponse
    {
        BotQuiz::where(['bot_id' => $botId])
            ->findOrFail($botQuiz)
            ->delete();

        return redirect()->back();
    }

    public function resort(int $botId, BotQuizResortRequest $request): void
    {
        foreach ($request->validated('quizzes') as $sortOrder => $id) {
            BotQuiz::query()
                ->where('bot_id', $botId)
                ->where('id', $id)
                ->update(['sort' => $sortOrder]);
        }
    }
}

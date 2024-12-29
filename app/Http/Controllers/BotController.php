<?php

namespace App\Http\Controllers;

use App\Enums\BotTypes;
use App\Http\Requests\Bots\BotRequest;
use App\Models\Bot;
use Illuminate\Http\RedirectResponse;

class BotController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Bots/Index', [
            'bots' => Bot::all()
        ]);
    }

    public function create(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Bots/Edit');
    }

    public function store(BotRequest $request): RedirectResponse
    {
        $bot = Bot::create($request->validated() + ['type' => 1]);

        return to_route('bots.show', $bot->id);
    }

    public function show(Bot $bot): \Inertia\Response|\Inertia\ResponseFactory
    {
        if($bot->type->isStandard()) {
            $bot->load(['greetings', 'triggers']);
        }

        if($bot->type->isQuiz()) {
            $bot->load(['quizzes']);
        }

        return inertia('Bots/Show', compact('bot'));
    }

    public function edit(Bot $bot): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Bots/Edit', compact('bot'));
    }

    public function update(Bot $bot, BotRequest $request): RedirectResponse
    {
        $bot->update($request->validated());

        return redirect()->back();
    }

    public function destroy(Bot $bot): RedirectResponse
    {
        $bot->delete();

        return to_route('bots.index');
    }

    public function changeActivity(Bot $bot): void
    {
        $bot->update(['is_active' => !$bot->is_active]);
    }

    public function changeType(Bot $bot, int $type): RedirectResponse
    {
        if(BotTypes::values()->contains($type) && $bot->type->value !== $type) {
            $bot->update(['type' => $type]);
        }

        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\BotRequest;
use App\Models\Bot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        Bot::create($request->validated() + ['type' => 1]);

        return to_route('bots.index');
    }

    public function show(Bot $bot): \Inertia\Response|\Inertia\ResponseFactory
    {
        $bot->load(['greetings', 'triggers']);

        return inertia('Bots/Show', compact('bot'));
    }

    public function edit(Bot $bot): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Bots/Edit', compact('bot'));
    }

    public function update(Bot $bot, BotRequest $request): RedirectResponse
    {
        $bot->update($request->validated());

        return to_route('bots.index');
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
}

<?php

namespace App\Http\Controllers;

use App\Enums\BotTypes;
use App\Http\Requests\BotAttachAccountRequest;
use App\Http\Requests\BotAttachAdRequest;
use App\Http\Requests\Bots\BotRequest;
use App\Jobs\ResetBotStates;
use App\Models\Account;
use App\Models\Ad;
use App\Models\Bot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

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
        $bot->load('accounts', 'schedules.slots');

        if ($bot->type->isStandard()) {
            $bot->load(['greetings', 'triggers']);
        }

        if ($bot->type->isQuiz()) {
            $bot->load(['quizzes']);
        }

        $accounts = Account::all();

        return inertia('Bots/Show', compact('bot', 'accounts'));
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
        if (BotTypes::values()->contains($type) && $bot->type->value !== $type) {
            $bot->update(['type' => $type]);

            ResetBotStates::dispatch($bot);
        }

        return redirect()->back();
    }

    public function attachAccounts(Bot $bot, BotAttachAccountRequest $request): void
    {
        $accounts = Account::whereIn('id', $request->accounts)->get();

        DB::transaction(static function () use ($bot, $accounts, $request) {
            $bot->accounts
                ->whereNotIn('id', $request->accounts)
                ->each
                ->update(['bot_id' => null]);

            $accounts->each(function ($account) use ($bot) {
                $account->update(['bot_id' => $bot->id]);
            });
        });
    }

    public function connectedAdTreeView(Bot $bot): JsonResponse
    {
        $accounts = Account::whereHas('ads')->with('ads')->get();

        $selected = $bot->ads()->pluck('external_id');

        $treeView = $accounts->map(function ($account) {
            return [
                'id' => $account->id,
                'title' => $account->name,
                'children' => $account->ads->map(function ($ad) {
                    return [
                        'id' => $ad->external_id,
                        'title' => $ad->title,
                    ];
                })
            ];
        });

        return response()->json([
            'selected' => $selected,
            'treeView' => $treeView,
        ]);
    }

    public function attachAds(Bot $bot, BotAttachAdRequest $request): RedirectResponse
    {
        $ads = Ad::whereIn('external_id', $request->ads)->get();

        DB::transaction(static function () use ($bot, $ads, $request) {
            $bot->ads
                ->whereNotIn('external_id', $request->ads)
                ->each
                ->update(['bot_id' => null]);

            $ads->each(function ($ad) use ($bot) {
                $ad->update(['bot_id' => $bot->id]);
            });
        });

        return redirect()->back();
    }
}

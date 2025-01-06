<?php

namespace App\Http\Controllers;

use App\Enums\BotTypes;
use App\Http\Requests\BotAttachAccountRequest;
use App\Http\Requests\BotAttachAdRequest;
use App\Http\Requests\Bots\BotRequest;
use App\Http\Requests\BotSettingRequest;
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
            'bots' => Bot::isOwner()->get()
        ]);
    }

    public function create(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Bots/Edit');
    }

    public function store(BotRequest $request): RedirectResponse
    {
        $bot = Bot::create($request->validated());

        return to_route('bots.show', $bot->id);
    }

    public function show(int $botId): \Inertia\Response|\Inertia\ResponseFactory
    {
        $bot = Bot::isOwner()->findOrFail($botId);

        $bot->load('accounts', 'schedules.slots');

        if ($bot->type->isStandard()) {
            $bot->load(['greetings', 'triggers']);
        }

        if ($bot->type->isQuiz()) {
            $bot->load(['quizzes' => fn($q) => $q->orderBy('sort')]);
        }

        $accounts = Account::isOwner()->get();

        return inertia('Bots/Show', compact('bot', 'accounts'));
    }

    public function edit(int $botId): \Inertia\Response|\Inertia\ResponseFactory
    {
        $bot = Bot::isOwner()->findOrFail($botId);

        return inertia('Bots/Edit', compact('bot'));
    }

    public function update(int $botId, BotRequest $request): RedirectResponse
    {
        $bot = Bot::isOwner()->findOrFail($botId);

        $bot->update($request->validated());

        if (in_array('type', $bot->getChanges(), true)) {
            ResetBotStates::dispatch($bot);
        }

        return redirect()->back();
    }

    public function destroy(int $botId): RedirectResponse
    {
        Bot::isOwner()->findOrFail($botId)->delete();

        return to_route('bots.index');
    }

    public function changeActivity(int $botId): void
    {
        $bot = Bot::isOwner()->findOrFail($botId);

        $bot->update(['is_active' => !$bot->is_active]);
    }

    public function changeType(int $botId, int $type): RedirectResponse
    {
        $bot = Bot::isOwner()->findOrFail($botId);

        if (BotTypes::values()->contains($type) && $bot->type->value !== $type) {
            $bot->update(['type' => $type]);

            ResetBotStates::dispatch($bot);
        }

        return redirect()->back();
    }

    public function attachAccounts(int $botId, BotAttachAccountRequest $request): void
    {
        $bot = Bot::isOwner()->findOrFail($botId);

        $accounts = Account::isOwner()->whereIn('id', $request->accounts)->get();

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

    public function connectedAdTreeView(int $botId): JsonResponse
    {
        $bot = Bot::isOwner()->findOrFail($botId);

        $accounts = Account::isOwner()->whereHas('ads')->with('ads')->get();

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

    public function attachAds(int $botId, BotAttachAdRequest $request): RedirectResponse
    {
        $bot = Bot::isOwner()->findOrFail($botId);
        $ads = Ad::relatedToMe()->whereIn('external_id', $request->ads)->get();

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

    public function updateSettings(int $botId, BotSettingRequest $request): RedirectResponse
    {
        $bot = Bot::isOwner()->findOrFail($botId);

        $bot->update($request->validated('settings'));

        return redirect()->back();
    }
}

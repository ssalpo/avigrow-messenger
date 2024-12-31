<?php

namespace App\Jobs;

use App\Models\Bot;
use App\Models\BotChatState;
use App\Models\BotQuizState;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ResetBotStates implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Bot $bot
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        BotChatState::whereIn('account_id', $this->bot->accounts->pluck('id'))->delete();
        BotQuizState::where('bot_id', $this->bot->id)->delete();
    }
}

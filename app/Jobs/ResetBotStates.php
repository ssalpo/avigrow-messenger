<?php

namespace App\Jobs;

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
        public int $botId
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        BotChatState::where('bot_id', $this->botId)->delete();
        BotQuizState::where('bot_id', $this->botId)->delete();
    }
}

<?php

namespace App\Console\Commands;

use App\Models\BotChatState;
use App\Models\BotQuizState;
use Illuminate\Console\Command;

class CloseInactiveQuizzes extends Command
{
    protected $signature = 'app:close-inactive-bot-states';
    protected $description = 'Закрытие неактивных квизов и приветствий.';


    public function handle(): void
    {
        $timeout = now()->subHours(24);

        BotQuizState::where('updated_at', '<', $timeout)->delete();
        BotChatState::where('updated_at', '<', $timeout)->delete();
    }
}

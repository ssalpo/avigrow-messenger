<?php

namespace App\Console\Commands;

use App\Models\BotQuizState;
use Illuminate\Console\Command;

class CloseInactiveQuizzes extends Command
{
    protected $signature = 'quiz:close-inactive';
    protected $description = 'Закрытие неактивных квизов.';


    public function handle()
    {
        $timeoutInHours = 24;

        BotQuizState::where('updated_at', '<', now()->subHours($timeoutInHours))->delete();
    }
}

<?php

namespace App\Console\Commands;

use App\Services\ActiveConversationService;
use Illuminate\Console\Command;

class ClearInactiveConversations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-inactive-conversations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает неактивные чаты';

    /**
     * Execute the console command.
     */
    public function handle(ActiveConversationService $service)
    {
        $service->clearInactive();
    }
}

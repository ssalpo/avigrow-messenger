<?php

namespace App\Jobs;

use App\Models\Analytic;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddChatToAnalytics implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int    $accountId,
        public string $chatId
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $conditions = ['account_id' => $this->accountId, 'chat_id' => $this->chatId];

        if (!Analytic::where($conditions)->exists()) {
            Analytic::create($conditions);
        }
    }
}

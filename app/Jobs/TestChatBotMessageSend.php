<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TestChatBotMessageSend implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $message
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info($this->message);
    }
}

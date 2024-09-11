<?php

namespace App\Jobs;

use App\Services\Telegram;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageToExistingIds implements ShouldQueue
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
        Telegram::sendMessageToExistIds($this->message);
    }
}

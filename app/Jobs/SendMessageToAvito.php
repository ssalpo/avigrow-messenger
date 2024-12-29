<?php

namespace App\Jobs;

use App\Models\Account;
use App\Services\Avito;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendMessageToAvito implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Account $account,
        public string  $chatId,
        public string  $message
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(Avito $avito): void
    {
        $avito
            ->setAccount($this->account)
            ->sendMessage($this->chatId, [
                'text' => $this->message
            ]);
    }
}

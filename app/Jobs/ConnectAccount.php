<?php

namespace App\Jobs;

use App\AccountConnectStatus;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\Avito;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConnectAccount implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Account $account
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AccountService $accountService): void
    {
        $accountService->connect($this->account);
    }
}

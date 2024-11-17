<?php

namespace App\Console\Commands;

use App\Services\AccountService;
use Illuminate\Console\Command;

class RefreshAccountsTokenInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-accounts-token-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Принудительно обновляет токены';

    /**
     * Execute the console command.
     */
    public function handle(AccountService $accountService)
    {
        $accountService->refreshAccountsTokenInfo();
    }
}

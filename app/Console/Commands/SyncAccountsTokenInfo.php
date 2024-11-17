<?php

namespace App\Console\Commands;

use App\Services\AccountService;
use Illuminate\Console\Command;

class SyncAccountsTokenInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-accounts-token-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получает токен (нужно в начале когда новый аккаунт добавляется)';

    /**
     * Execute the console command.
     */
    public function handle(AccountService $accountService)
    {
        $accountService->syncAccountsTokenInfo();
    }
}

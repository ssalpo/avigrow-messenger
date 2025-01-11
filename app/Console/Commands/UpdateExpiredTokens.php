<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет устаревшие токены, так как токены авито живет только день.';

    /**
     * Execute the console command.
     */
    public function handle(AccountService $accountService)
    {
        // Обновляет устаревшие токены за час до окончания времени жизни токена
        $accounts = Account::where('external_access_token_expire_date', '<', now('Europe/Moscow')->subHour())->get();

        $accounts
            ->filter(fn($a) => $a->token_refreshed_at)
            ->each(fn($a) => $accountService->changeAccountToken(account: $a, isRefresh: true));
    }
}

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
        $accounts = Account::all();

        foreach ($accounts as $account) {
            if (!$account->token_refreshed_at) {
                continue;
            }

            $current = Carbon::now()->addMinutes($account->external_access_token_expire_in);
            $expired = Carbon::now()->addMinutes($account->external_access_token_expire_in - 20);

            if ($current > $expired) {
                $accountService->changeAccountToken(
                    $account,
                    !is_null($account->external_refresh_token)
                );
            }
        }
    }
}

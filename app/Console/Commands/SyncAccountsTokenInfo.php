<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\Avito;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Avito $avito)
    {
        $accounts = Account::all();

        $accounts->each(function ($account) use ($avito){
            $token = $avito->setAccount($account)->getToken();

            $account->update([
                'external_access_token' => $token['access_token'],
                'external_access_token_expire_in' => $token['expires_in']
            ]);
        });
    }
}

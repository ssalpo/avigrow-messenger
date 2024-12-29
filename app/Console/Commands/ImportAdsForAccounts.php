<?php

namespace App\Console\Commands;

use App\Jobs\ImportAdForAccount;
use App\Models\Account;
use App\Services\Ads\AdImporter;
use Illuminate\Console\Command;

class ImportAdsForAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-ads-for-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(AdImporter $importer): void
    {
        $activeAccounts = Account::all();

        foreach ($activeAccounts as $account) {
            ImportAdForAccount::dispatch($account);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Events\NewMessage;
use App\Models\Account;
use App\Services\Avito;
use Illuminate\Console\Command;

class SyncAccountsWebhookSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-accounts-webhook-subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Формирует вебхук адрес для нашей системы (при смене токена на одном из аккаунтов, повторно запустить регистрацию вебхука)';

    /**
     * Execute the console command.
     */
    public function handle(Avito $avito)
    {
        $accounts = Account::all();

        $accounts->each(function ($account) use ($avito){
            $avito->setAccount($account)->subscribeToWebhook();
        });
    }
}

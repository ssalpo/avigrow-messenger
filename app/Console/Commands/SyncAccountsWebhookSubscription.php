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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Avito $avito)
    {
        NewMessage::dispatch(22222, ['some' => 'data']);

//        $accounts = Account::all();
//
//        $accounts->each(function ($account) use ($avito){
//            $avito->setAccount($account)->subscribeToWebhook();
//        });
    }
}

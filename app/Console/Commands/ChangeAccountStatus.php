<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\Avito;
use Illuminate\Console\Command;

class ChangeAccountStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-account-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда затычка, чтобы менять статус аккаунта на онлайн';

    /**
     * Execute the console command.
     */
    public function handle(Avito $avito)
    {
        $accounts = Account::all();

        $accounts->each(function ($account) use($avito) {
            $avito->setAccount($account)->getChats(10);
        });
    }
}

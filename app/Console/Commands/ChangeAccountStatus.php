<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\AccountStatus;
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
        $statuses = AccountStatus::with('account')
            ->active()
            ->where(function ($q) {
                $q->where('always_online', true)
                    ->orWhere(
                        fn($q) => $q->whereNotNull('available_from')
                            ->whereNotNull('available_to')
                    );
            })
            ->get();

        // Always online
        $statuses
            ->filter(fn($s) => $s->always_online)
            ->chunk(10)
            ->each(fn($statuses) => $avito->markAsOnline($statuses));

        // Scheduled
        $statuses
            ->filter(function ($s) {
                $timezone = $s->account->timezone;

                $now = now($timezone);
                $start = now($timezone)->setTimeFrom($s->available_from);
                $end = now($timezone)->setTimeFrom($s->available_to);

                return !$s->always_online && $now->between($start, $end);
            })
            ->chunk(10)
            ->each(fn($statuses) => $avito->markAsOnline($statuses));
    }
}

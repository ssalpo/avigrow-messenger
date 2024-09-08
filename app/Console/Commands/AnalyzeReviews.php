<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\Avito;
use Illuminate\Console\Command;

class AnalyzeReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:analyze-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Анализирует сообщения и отзывы, чтобы найти тех, кто не оставил сообщения';

    /**
     * Execute the console command.
     */
    public function handle(Avito $avito)
    {
        // Получаем данные аккаунта
        // Получаем список чатов на анализ
        // Получаем список отзывов за вчера
        // Получаем список имен
        // Получаем список имен из авито
        // Сопоставляем тех кого нету в списке и отправляем в телеграм
        //

        $accounts = Account::with('analyzeReviews')->get();

        $accounts->each(function (Account $account) use ($avito) {

        });
    }
}

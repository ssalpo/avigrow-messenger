<?php

namespace App\Services;

use App\Models\Transaction;
use App\TransactionType;
use Illuminate\Support\Carbon;

class TransactionService
{
    public function getStatisticsByType(TransactionType $transactionType): array
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();

        // Сумма транзакций за день
        $daySum = Transaction::whereDate('created_at', $today)->where('type', $transactionType->value)
            ->sum('amount');

        // Сумма транзакций за неделю (без учета времени)
        $weekSum = Transaction::whereDate('created_at', '>=', $weekStart)->where('type', $transactionType->value)
            ->sum('amount');

        // Сумма транзакций за месяц (без учета времени)
        $monthSum = Transaction::whereDate('created_at', '>=', $monthStart)->where('type', $transactionType->value)
            ->sum('amount');

        return [
            $daySum,
            $weekSum,
            $monthSum,
        ];
    }
}

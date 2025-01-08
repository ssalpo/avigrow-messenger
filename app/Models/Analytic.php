<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Analytic extends Model
{
    protected $fillable = [
        'account_id',
        'chat_id'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public static function getDailyAnalyticsForAccounts(array $accountIds = [], $month = null, $year = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $startDate = Carbon::create($year, $month)->startOfDay()->toDateTimeString();
        $endDate = Carbon::create($year, $month)->endOfMonth()->endOfDay()->toDateTimeString();

        // Строим запрос
        $query = self::query()
            ->selectRaw('account_id, DAY(created_at) as day, COUNT(chat_id) as total_contacts')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('account_id', $accountIds)
            ->groupBy('account_id', 'day')
            ->orderBy('day');

        return $query->get()
            ->groupBy('account_id')
            ->map
            ->pluck('total_contacts', 'day');
    }
}

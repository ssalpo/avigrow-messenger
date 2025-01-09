<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Analytic;

class AnalyticController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $filters = request()->all();
        $accountList = Account::isOwner()->pluck('name', 'id');
        $selectedAccounts = array_map('intval', (array)request('accounts', []));

        if (count($selectedAccounts) > 0) {
            $selectedAccounts = $accountList->keys()->filter(fn($a) => in_array($a, $selectedAccounts, true));
        } else {
            $selectedAccounts = $accountList->keys();
        }

        $analytics = Analytic::getDailyAnalyticsForAccounts(
            $selectedAccounts->toArray(),
            (int) request()->input('date.month') + 1,
            request()->input('date.year'),
        );

        return inertia('Analytics', compact('analytics', 'accountList', 'filters'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Analytic;

class AnalyticController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $accounts = Account::pluck('name', 'id');

        $analytics = Analytic::getDailyAnalyticsForAccounts(
            \request('accounts', [1, 2, 3]),
            \request('month'),
            \request('year'),
        );

        return inertia('Analytics', compact('analytics', 'accounts'));
    }
}

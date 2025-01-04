<?php

namespace App\Http\Middleware;

use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $accounts = [];
        $activeAccount = [];

        if (auth()->check()) {
            $accounts = Account::all();

            $activeAccount = $request->route()->hasParameter('account')
                ? Account::findOrFail($request->route()->parameter('account'))
                : $accounts->first();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'accounts' => $accounts,
            'activeAccount' => $activeAccount,
            'backData' => $request->session()->get('backData'),
        ];
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Account;
use App\Services\UserService;
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
        if (auth()->check()) {
            $accounts = Account::relatedToMe()->get();

            if ($accounts->count()) {
                if ($account = (int)$request->route('account')) {
                    $activeAccount = $accounts->where('id', $account)->firstOrFail();
                } else {
                    $activeAccount = $accounts->first();
                }

                $request->attributes->set('activeAccount', $activeAccount);
                $request->attributes->set('currentCompany', $activeAccount->company);
                $request->attributes->set('currentCompanyId', $activeAccount->company->id);
            }

            return [
                ...parent::share($request),
                'auth' => [
                    'user' => $request->user(),
                ],
                'navAccounts' => $accounts,
                'activeAccount' => $activeAccount ?? null,
                'backData' => $request->session()->get('backData'),
            ];
        }

        return parent::share($request);
    }
}

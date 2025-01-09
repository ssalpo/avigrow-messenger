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
        if (auth()->check()) {
            $companies = \auth()->user()->companies->map->only(['id', 'name']);
            $selectedCompany = (int) ($request->get('selectedCompanyId') ?? $request->session()->get('selectedCompanyId') ?? $companies[0]['id']);

            if($request->session()->get('selectedCompanyId') !== $selectedCompany) {
                $request->session()->put('selectedCompanyId', $selectedCompany);
            }

            $accounts = Account::whereCompanyId($selectedCompany)->get();

            $activeAccount = $request->route()?->hasParameter('account')
                ? Account::whereCompanyId($selectedCompany)->findOrFail($request->route()?->parameter('account'))
                : $accounts->first();

            $request->attributes->set('activeAccount', $activeAccount);

            return [
                ...parent::share($request),
                'auth' => [
                    'user' => $request->user(),
                ],
                'navAccounts' => $accounts,
                'navCompanies' => $companies,
                'selectedCompany' => $selectedCompany,
                'activeAccount' => $activeAccount,
                'backData' => $request->session()->get('backData'),
            ];
        }

        return parent::share($request);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Http\Requests\AccountSettingsRequest;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\Avito;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService
    )
    {
    }

    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Accounts/Index', [
            'accounts' => Account::isOwner()->orderByDesc('created_at')->get()
        ]);
    }

    public function create(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Accounts/Edit');
    }

    public function store(AccountRequest $request): RedirectResponse|Response
    {
        $companyId = auth()->user()->myCompany->id;

        $account = $this->accountService->store($request->validated() + ['company_id' => $companyId]);

        if ($account->type->isFree()) {
            return Inertia::location(
                Avito::buildOAuthLink($account->oauth_check_key)
            );
        }

        return to_route('accounts.show', $account->id);
    }

    public function show(int $accountId): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Accounts/Show', [
            'account' => Account::isOwner()->findOrFail($accountId)
        ]);
    }

    public function edit(int $accountId): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Accounts/Edit', [
            'account' => Account::isOwner()->findOrFail($accountId)
        ]);
    }

    public function update(int $accountId, AccountRequest $request): RedirectResponse|Response
    {
        $account = $this->accountService->update($accountId, $request->validated());

        if (is_string($account)) {
            return Inertia::location($account);
        }

        return to_route('accounts.show', $accountId);
    }

    public function saveSettings(int $accountId, AccountSettingsRequest $request): RedirectResponse
    {
        $account = Account::isOwner()->findOrFail($accountId);

        $account->update($request->validated());

        return redirect()->back();
    }

    public function toggleActivity(int $accountId): void
    {
        $this->accountService->toggleActivity($accountId);
    }
}

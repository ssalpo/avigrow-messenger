<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\RedirectResponse;

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
            'accounts' => Account::orderByDesc('created_at')->get()
        ]);
    }

    public function create(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Accounts/Edit');
    }

    public function store(AccountRequest $request): RedirectResponse
    {
        $account = $this->accountService->store($request->validated());

        return to_route('accounts.show', $account->id);
    }

    public function show(int $accountId): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Accounts/Show', [
            'account' => Account::findOrFail($accountId)
        ]);
    }

    public function edit(int $accountId): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Accounts/Edit', [
            'account' => Account::findOrFail($accountId)
        ]);
    }

    public function update(int $accountId, AccountRequest $request): RedirectResponse
    {
        $this->accountService->update($accountId, $request->validated());

        return to_route('accounts.show', $accountId);
    }
}

<?php

namespace App\Services;

use App\Models\Account;

class AccountService
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    public function changeAccountToken(Account $account, bool $isRefresh = false): void
    {
        $this->avito->setAccount($account);

        $token = $isRefresh
            ? $this->avito->refreshExistToken($account->external_refresh_token)
            : $this->avito->getToken();

        if (isset($token['error'])) {
            return;
        }

        $data = [
            'external_access_token' => $token['access_token'],
            'external_access_token_expire_in' => $token['expires_in'],
            'token_refreshed_at' => now()
        ];

        if ($isRefresh) {
            $data['external_refresh_token'] = $token['refresh_token'];
        }

        $account->update($data);
    }

    public function syncAccountsTokenInfo(): void
    {
        $accounts = Account::query()->whereNull('external_refresh_token')->get();

        $accounts->each(fn($a) => $this->changeAccountToken($a));
    }

    public function refreshAccountsTokenInfo(): void
    {
        $accounts = Account::query()->whereNotNull('external_refresh_token')->get();

        $accounts->each(fn($a) => $this->changeAccountToken($a, true));
    }
}

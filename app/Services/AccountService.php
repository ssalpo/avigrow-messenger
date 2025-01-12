<?php

namespace App\Services;

use App\Enums\AccountConnectStatus;
use App\Jobs\ConnectAccount;
use App\Models\Account;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        $token = $isRefresh && $account->external_refresh_token
            ? $this->avito->refreshExistToken($account->external_refresh_token)
            : $this->avito->getToken();

        if (isset($token['error'])) {
            return;
        }

        $data = [
            'external_access_token' => $token['access_token'],
            'external_access_token_expire_in' => $token['expires_in'],
            'external_access_token_expire_date' => now()->addSeconds($token['expires_in'])->subHours(4),
            'token_refreshed_at' => now()
        ];

        if ($isRefresh && $account->external_refresh_token) {
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

    public function store(array $data): Account
    {
        $account = Account::create($data + ['webhook_handle_token' => Str::random(32)]);

        ConnectAccount::dispatch($account);

        return $account;
    }

    public function reconnect(int $accountId): void
    {
        $account = Account::findOrFail($accountId);

        $account->update([
            'connection_status' => AccountConnectStatus::CONNECTION_PENDING
        ]);

        ConnectAccount::dispatch($account);
    }

    public function connect(Account $account): void
    {
        try {
            // Подтягиваем токены
            $this->changeAccountToken($account);

            $me = $this->avito->me();

            // Меняем статус подключения аккаунта и обновляем данные профиля
            $account->forceFill([
                'external_id' => $me->id,
                'avito_name' => $me->name,
                'avito_profile_url' => $me->profileUrl,
                'connection_status' => AccountConnectStatus::CONNECTED
            ])->save();
        } catch (\Exception $exception) {
            $account->forceFill([
                'connection_status' => AccountConnectStatus::CONNECTION_ERROR,
                'connection_errors' => $exception->getMessage()
            ])->save();
        }
    }

    public function update(int $accountId, array $data): Account
    {
        $account = Account::isOwner()->findOrFail($accountId);

        $account->update($data);

        $fieldChangeToDetect = [
            'external_client_id', 'external_client_secret'
        ];

        if (!empty(array_intersect(array_keys($account->getChanges()), $fieldChangeToDetect))) {
            $this->reconnect($accountId);
        }

        return $account;
    }
}

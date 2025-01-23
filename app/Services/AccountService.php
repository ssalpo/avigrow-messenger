<?php

namespace App\Services;

use App\Enums\AccountConnectStatus;
use App\Enums\AccountType;
use App\Jobs\ConnectAccount;
use App\Models\Account;
use Illuminate\Support\Arr;
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
            'external_access_token_expire_date' => now()->addSeconds($token['expires_in']),
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
        $account = Account::create($data + [
                'webhook_handle_token' => Str::random(32),
                'oauth_check_key' => Str::random(32),
            ]);

        if ($account->type->isPro()) {
            ConnectAccount::dispatch($account);
        }

        return $account;
    }

    public function reconnect(int $accountId): void
    {
        $account = Account::findOrFail($accountId);

        $account->update([
            'connection_status' => AccountConnectStatus::CONNECTION_PENDING
        ]);

        if ($account->type->isPro()) {
            ConnectAccount::dispatch($account);
        }
    }

    public function connect(Account $account): Account
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

        return $account;
    }

    public function connectOAuth(string $state, string $code): Account
    {
        $account = Account::findByOAuthKey($state);

        try {
            $response = (new Avito)->getTokenByCode($code);

            $account->fill([
                'external_access_token' => $response['access_token'],
                'external_refresh_token' => $response['refresh_token'],
                'external_access_token_expire_in' => $response['expires_in'],
                'oauth_check_key' => Str::random(32),
            ]);

            $me = $this->avito->setAccount($account)->me();

            $account->fill([
                'external_id' => $me->id,
                'avito_name' => $me->name,
                'avito_profile_url' => $me->profileUrl,
                'connection_status' => AccountConnectStatus::CONNECTED
            ]);

            $account->save();
        } catch (\Exception $exception) {
            $account->forceFill([
                'connection_status' => AccountConnectStatus::CONNECTION_ERROR,
                'connection_errors' => $exception->getMessage()
            ])->save();
        }

        return $account;
    }

    public function update(int $accountId, array $data): Account|string
    {
        $account = Account::isOwner()->findOrFail($accountId);

        if ($data['type'] === AccountType::FREE->value && $account->type->isPro()) {
            $account->update(Arr::only($data, ['type', 'name']));

            return Avito::buildOAuthLink($account->oauth_check_key);
        }

        dd($data['type'] === AccountType::FREE,$data['type'], $account->type->isPro());

        $account->update($data);

        $fieldChangeToDetect = [
            'external_client_id', 'external_client_secret'
        ];

        $isSecretsChange = !empty(array_intersect(array_keys($account->getChanges()), $fieldChangeToDetect));

        if ($isSecretsChange && $account->type->isPro()) {
            $this->reconnect($accountId);
        }

        return $account;
    }

    public function toggleActivity(int $accountId): void
    {
        $account = Account::isOwner()->findOrFail($accountId);

        $account->update(['is_active' => !$account->is_active]);

        $this->avito->setAccount($account);

        if ($account->is_active) {
            $this->avito->subscribeToWebhook();
        } else {
            $this->avito->unsubscribeFromWebhook();
        }
    }
}

<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Avito
{
    protected Account $account;

    public const BASE_API_URL = 'https://api.avito.ru';

    protected function client(): PendingRequest
    {
        return Http::baseUrl(self::BASE_API_URL)
            ->asJson()
            ->timeout(120)
            ->retry(2, 3000);
    }

    protected function clientWithToken(): PendingRequest
    {
        return $this->client()
            ->withToken($this->account->external_access_token);
    }

    public static function validateTelegramWebAppData(string $queryString, string $botToken): bool
    {
        // Преобразуем строку запроса в массив
        parse_str($queryString, $data);

//        // Проверяем наличие обязательных полей
//        if (!isset($data['hash'], $data['auth_date'])) {
//            return false;
//        }
//
//        // Проверяем, не устарели ли данные (например, 1 час)
//        $authDate = (int)$data['auth_date'];
//
//        if (time() - $authDate > 3600) {
//            return false;
//        }

        // Формируем data-check-string
        $checkString = collect($data)
            ->except('hash')
            ->map(fn($value, $key) => "$key=$value")
            ->sortKeys()
            ->implode("\n");

        // Генерируем секретный ключ
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);

        // Вычисляем хэш
        $calculatedHash = hash_hmac('sha256', $checkString, $secretKey);

        // Сравниваем хэши
        return hash_equals($data['hash'], $calculatedHash);
    }

    public function setAccount(Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getToken(): array
    {
        $credentials = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->account->external_client_id,
            'client_secret' => $this->account->external_client_secret
        ];

        return $this->clientWithToken()->post("/token?" . http_build_query($credentials))->json();
    }

    public function getTokenByCode(string $code): array
    {
        $credentials = [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.avito.oauth.clientId'),
            'client_secret' => config('services.avito.oauth.clientSecret'),
            'code' => $code
        ];

        return $this->client()->asForm()->post("/token", $credentials)->json();
    }

    public function refreshExistToken(string $refreshToken): array
    {
        $credentials = [
            'grant_type' => 'refresh_token',
            'client_id' => config('services.avito.oauth.clientId'),
            'client_secret' => config('services.avito.oauth.clientSecret'),
            'refresh_token' => $refreshToken
        ];

        return $this->client()->asForm()->post("/token", $credentials)->json();
    }

    public function subscribeToWebhook(): void
    {
        $this->clientWithToken()->post('/messenger/v3/webhook', [
            'url' => $this->webhookUrl()
        ]);
    }

    public function unsubscribeFromWebhook(Account $account): void
    {
        $this->setAccount($account);

        $this->clientWithToken()->post('/messenger/v1/webhook/unsubscribe', [
            'url' => $this->webhookUrl()
        ]);
    }

    public function listOfWebhookSubscriptions(): array
    {
        return $this->clientWithToken()
            ->post('/messenger/v1/subscriptions')
            ->json('subscriptions') ?? [];
    }

    public function webhookUrl(): string
    {
        return config('app.url') . "/api/webhook/{$this->account->id}";
    }

    public function getChats(int $limit = 50, int $page = 1): array
    {
        $offset = ceil(($page - 1) * $limit);

        return $this->clientWithToken()
            ->get("/messenger/v2/accounts/{$this->account->external_id}/chats?limit=$limit&offset=$offset&chat_types=u2i,u2u")
            ->json() ?? [];
    }

    public function getUnreadChats(): array
    {
        return $this->clientWithToken()
            ->get("/messenger/v2/accounts/{$this->account->external_id}/chats?unread_only=true&chat_types=u2i,u2u")
            ->json() ?? [];
    }

    public function getUnreadChatIds(): array
    {
        $unreadChats = $this->getUnreadChats()['chats'] ?? [];

        return array_map(fn($ch) => $ch['id'], $unreadChats);
    }

    public function getChatInfoById(string $chatId)
    {
        return $this->clientWithToken()
            ->get("/messenger/v2/accounts/{$this->account->external_id}/chats/$chatId")
            ->json() ?? [];
    }

    public function markChatAsRead(string $chatId): void
    {
        $this->clientWithToken()->post(
            "/messenger/v1/accounts/{$this->account->external_id}/chats/{$chatId}/read"
        );
    }

    public function getChatMessages(string $chatId, int $limit = 30, int $page = 1): array
    {
        $offset = ceil(($page - 1) * $limit);

        return $this->clientWithToken()
            ->get("/messenger/v3/accounts/{$this->account->external_id}/chats/$chatId/messages?limit=$limit&offset=$offset")
            ->json() ?? [];
    }

    public function sendMessage(string $chatId, array $message): array
    {
        return $this->clientWithToken()->post(
            "/messenger/v1/accounts/{$this->account->external_id}/chats/{$chatId}/messages",
            [
                'message' => $message,
                'type' => 'text'
            ]
        )->json() ?? [];
    }

    public function deleteMessage(string $chatId, string $messageId): void
    {
        $this->clientWithToken()->post(
            "/messenger/v1/accounts/{$this->account->external_id}/chats/{$chatId}/messages/{$messageId}"
        );
    }

    public function me(): array
    {
        return $this->clientWithToken()->get("/core/v1/accounts/self")->json() ?? [];
    }

    public static function getMessageBasedOnType(array $data)
    {
        return match ($data['type']) {
            'text', 'system' => $data['content']['text'],
            'call' => 'Звонок',
            'image' => 'Фото',
            'item' => 'Объявление',
            'link' => 'Ссылка',
            'location' => 'Локация',
            'video' => 'Видео',
            'file' => 'Файл',
        };
    }

    public function reviews(int $limit = 10, int $page = 1)
    {
        $offset = ceil(($page - 1) * $limit);

        return $this->clientWithToken()->get("/ratings/v1/reviews?offset=$offset&limit=$limit")->json() ?? [];
    }
}

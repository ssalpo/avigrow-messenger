<?php

namespace App\Services;

use App\Models\Account;
use App\Services\DTO\Avito\AvitoAuthUserDto;
use App\Services\DTO\Avito\AvitoChatDto;
use App\Services\DTO\Avito\AvitoChatUserDto;
use App\Services\DTO\Avito\AvitoWebhookPayloadDto;
use GuzzleHttp\Client;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Avito
{
    protected Account $account;

    public const BASE_API_URL = 'https://api.avito.ru';

    protected function client(bool $asJsonBody = true): PendingRequest
    {
        $client = Http::baseUrl(self::BASE_API_URL)
            ->timeout(120)
            ->retry(2, 3000);

        if ($asJsonBody) {
            return $client->asJson();
        }

        return $client;
    }

    protected function clientWithToken(bool $asJsonBody = true): PendingRequest
    {
        return $this->client($asJsonBody)
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

    public function unsubscribeFromWebhook(?string $url = null): void
    {
        $this->clientWithToken()->post('/messenger/v1/webhook/unsubscribe', [
            'url' => $url ?? $this->webhookUrl()
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
        return config('app.url') . "/api/webhook/{$this->account->id}/{$this->account->webhook_handle_token}";
    }

    public function getChats(int $limit = 50, int $page = 1): array
    {
        $offset = ceil(($page - 1) * $limit);
        $url = "/messenger/v2/accounts/{$this->account->external_id}/chats?limit=$limit&offset=$offset&chat_types=u2i,u2u";

        return $this->clientWithToken()->get($url)->json();
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

    public function getChatInfoById(string $chatId): AvitoChatDto
    {
        return AvitoChatDto::fromArray(
            $this->clientWithToken()
                ->get("/messenger/v2/accounts/{$this->account->external_id}/chats/$chatId")
                ->json()
        );
    }

    public function markChatAsRead(string $chatId): void
    {
        ActiveConversationService::sync($this->account, $chatId);

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

    public function me(): AvitoAuthUserDto
    {
        return AvitoAuthUserDto::fromArray(
            $this->clientWithToken()->get("/core/v1/accounts/self")->json()
        );
    }

    public static function getMessageBasedOnType(AvitoWebhookPayloadDto $payloadDto)
    {


        return match ($payloadDto->type) {
            'text', 'system' => $payloadDto->content['text'],
            'call' => 'Звонок',
            'image' => 'Фото',
            'item' => 'Объявление',
            'link' => 'Ссылка',
            'location' => 'Локация',
            'video' => 'Видео',
            'file' => 'Файл',
            'voice' => 'Голосовое сообщение',
            default => 'Не поддерживаемый тип контента: ' . $payloadDto->type
        };
    }

    public function reviews(int $limit = 10, int $page = 1)
    {
        $offset = ceil(($page - 1) * $limit);

        return $this->clientWithToken()->get("/ratings/v1/reviews?offset=$offset&limit=$limit")->json() ?? [];
    }

    public function sendAnswerToReview(int $reviewId, string $message): array
    {
        return $this->clientWithToken()->post("/ratings/v1/answers", [
            'reviewId' => $reviewId,
            'message' => $message,
        ])->json();
    }

    public function deleteReviewAnswer(int $answerId): array
    {
        return $this->clientWithToken()->delete("/ratings/v1/answers/$answerId")->json();
    }

    public static function getUserFromChat(array $users, string $accountId): AvitoChatUserDto
    {
        $user = collect($users)->where('id', '!=', $accountId)->first();

        return AvitoChatUserDto::fromArray($user);
    }

    public static function getPriceFromChat(array $chat): ?array
    {
        return str_replace(' ₽', '', $chat['context']['value']['price_string']);
    }

    public static function chatResponse(AvitoChatDto $chatDto, Account $account): array
    {
        $user = self::getUserFromChat($chatDto->users, $account->external_id);

        return [
            'id' => $chatDto->id,
            'context_id' => $chatDto->id,
            'context' => $chatDto->item->title,
            'image' => $user->avatarOthers['140x105'] ?? $user->avatar ?? null,
            'price' => $chatDto->item->price ?? '',
            'url' => $chatDto->item->url,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
            ],
            'last_message' => [
                'id' => $chatDto->lastMessage->id,
                'content_type' => $chatDto->lastMessage->type,
                'content' => $chatDto->lastMessage->content,
                'created' => $chatDto->lastMessage->created,
                'is_read' => $chatDto->lastMessage->isRead
            ]
        ];
    }

    public function getItems(int $perPage = 100, int $page = 1): array
    {
        return $this->clientWithToken()
            ->get("/core/v1/items", [
                'per_page' => $perPage,
                'page' => $page
            ])
            ->json();
    }

    public function uploadImage(string $filePath, string $fileName): array
    {
        $userId = $this->account->external_id;

        $response = $this->clientWithToken(false)
            ->asMultipart()
            ->attach('uploadfile[]', fopen($filePath, 'rb'), $fileName)
            ->post("/messenger/v1/accounts/$userId/uploadImages");

        $imageId = key($response->json());

        return [
            'id' => $imageId,
            $response->json($imageId)
        ];
    }

    public function sendImageMessage(string $chatId, string $imageId): array
    {
        return $this->clientWithToken()->post(
            "/messenger/v1/accounts/{$this->account->external_id}/chats/$chatId/messages/image",
            [
                'image_id' => $imageId,
            ]
        )->json();
    }
}

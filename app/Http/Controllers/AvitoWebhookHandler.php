<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Jobs\AddChatToAnalytics;
use App\Jobs\AddToAnalyzeReviews;
use App\Jobs\HandleChatBotMessage;
use App\Jobs\SendMessageToTelegram;
use App\Models\Account;
use App\Services\Avito;
use App\Services\DTO\Avito\AvitoWebhookPayloadDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AvitoWebhookHandler extends Controller
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(int $accountId, string $webhookHandleToken, Request $request): void
    {
        logger()->info(json_encode($request->post('payload', [])));

        $account = Account::where(['id' => $accountId, 'webhook_handle_token' => $webhookHandleToken])->firstOrFail();

        $payload = AvitoWebhookPayloadDto::fromArray($request->post('payload', []));

        $isMe = $payload->authorId === (int)$account->external_id;

        if (!$isMe && $account->telegram_chat_id) {
            SendMessageToTelegram::dispatch(
                $account->id,
                $payload->chatId,
                $payload->chatType,
                $payload
            );
        }

        if (!$isMe) {
            HandleChatBotMessage::dispatch($payload, $account);
        }

        NewMessage::dispatch($account->id, [
            'unreadChatIds' => $this->avito->setAccount($account)->getUnreadChatIds(),
            'message' => [
                'id' => $payload->id,
                'chat_id' => $payload->chatId,
                'content' => $payload->content,
                'isRead' => $payload->isRead,
                'created' => $payload->created,
                'created_at' => $payload->createdAtFormated(),
                'is_me' => $isMe,
                'type' => $payload->type,
            ]
        ]);

        AddToAnalyzeReviews::dispatch($account->id, $payload->chatId);

        $this->addToAnalytics($account->id, $payload->chatId);
    }

    private function addToAnalytics(int $accountId, string $chatId): void
    {
        $key = 'analytics:' . $chatId;

        if (!Cache::has($key)) {
            Cache::put($key, '1', now()->addDays(2));

            AddChatToAnalytics::dispatch($accountId, $chatId);
        }
    }
}

<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageVoiceHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        $message = 'Голосовое сообщение';

        Telegram::sendMessage(
            $params['currentTelegramChatId'],
            view('telegram.webhook-message', $params + ['message' => $message])->render()
        );
    }
}

<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageVideoHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        $message = 'Видео';

        Telegram::sendMessage(
            $params['currentTelegramChatId'],
            view('telegram.webhook-message', $params + ['message' => $message])->render()
        );
    }
}

<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageFileHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        $message = 'Файл';

        Telegram::sendMessage(
            $params['currentTelegramChatId'],
            view('telegram.webhook-message', $params + ['message' => $message])->render()
        );
    }
}

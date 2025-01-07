<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageCallHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        $message = 'Звонок';

        Telegram::sendMessage(
            $params['currentTelegramChatId'],
            view('telegram.webhook-message', $params + ['message' => $message])->render()
        );
    }
}

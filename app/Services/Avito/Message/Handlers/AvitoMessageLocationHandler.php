<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageLocationHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        $message = 'Локация: ' . $content['location']['text'];

        Telegram::sendMessage(
            $params['currentTelegramChatId'],
            view('telegram.webhook-message', $params + ['message' => $message])->render()
        );
    }
}

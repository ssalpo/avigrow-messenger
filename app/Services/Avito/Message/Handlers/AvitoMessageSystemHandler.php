<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageSystemHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        Telegram::sendMessageToExistIds(
            view('telegram.webhook-message', $params + ['message' => $content['text']])->render()
        );
    }
}

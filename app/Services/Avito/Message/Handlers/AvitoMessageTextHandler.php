<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageTextHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        $message = view('telegram.webhook-message', $params)->render();

        Telegram::sendMessageToExistIds($message);
    }
}

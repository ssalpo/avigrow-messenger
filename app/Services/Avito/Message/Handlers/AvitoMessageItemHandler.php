<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageItemHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        $message = view('telegram.handlers.item', [
            'itemUrl' => $content['item']['url'],
            'itemTitle' => $content['item']['title'],
            'itemPrice' => $content['item']['price']
        ])->render();

        Telegram::sendMessageToExistIds(
            view('telegram.webhook-message', $params + ['message' => $message])->render()
        );
    }
}

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
            'itemPrice' => $content['item']['price_string']
        ])->render();

        Telegram::sendMessage(
            $params['currentTelegramChatId'],
            view('telegram.webhook-message', $params + ['message' => $message])->render()
        );
    }
}

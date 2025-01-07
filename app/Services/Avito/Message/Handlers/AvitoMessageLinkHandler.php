<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageLinkHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        $message = <<<MSG
Ссылка:

<a href="{$content['link']['url']}">{$content['link']['text']}</a>
MSG;

        Telegram::sendMessage(
            $params['currentTelegramChatId'],
            view('telegram.webhook-message', $params + ['message' => $message])->render()
        );
    }
}

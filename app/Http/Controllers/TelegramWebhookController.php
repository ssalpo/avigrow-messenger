<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\Avito;
use App\Services\Telegram;
use Illuminate\Support\Arr;

class TelegramWebhookController extends Controller
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    /**
     * Чтобы установить вебхук или удалить воспользуйтесь примером
     *
     * https://api.telegram.org/bot{TokenBot}/setWebhook?url=https://{url}/api/t-webhook
     *
     * https://api.telegram.org/bot{TokenBot}/deleteWebhook?url=https://{url}/api/t-webhook
     */
    public function __invoke(): void
    {
        $input = request()->all();

        logger()?->info(json_encode($input));

        if ($this->isIdCommandSend($input)) {
            $this->handleIdCommand($input);
            return;
        }

        $this->handleReply($input);
    }

    public function handleReply(array $input): void
    {
        $message = $this->getMessage($input);

        if (!isset($message['reply_to_message']['text'])) {
            return;
        }

        $accountInfo = $this->accountInfo($message['reply_to_message']['text']);

        if (!isset($accountInfo['accountId']) || !isset($accountInfo['chatId'])) {
            return;
        }

        $account = Account::findOrFail($accountInfo['accountId']);

        $fromId = (string) (Arr::get($message, 'from.id') ?? Arr::get($message, 'sender_chat.id'));

        if ($account->telegram_chat_id !== $fromId) {
            return;
        }

        $this->avito
            ->setAccount($account)
            ->sendMessage($accountInfo['chatId'], ['text' => $message['text']]);
    }

    private function accountInfo(string $text): ?array
    {
        if (preg_match('/\[(\d+),(.+?)\]/', $text, $matches)) {
            return [
                'accountId' => $matches[1],
                'chatId' => $matches[2],
            ];
        }

        return null;
    }

    private function isIdCommandSend(array $input): bool
    {
        return $this->getMessage($input, 'text') === '/id';
    }

    private function handleIdCommand(array $input): void
    {
        $chatId = $this->getMessage($input, 'chat.id');

        Telegram::sendMessage($chatId, $chatId);
    }

    private function getMessage(array $input, ?string $currentField = null)
    {
        $fields = ['message', 'channel_post', 'edited_channel', 'edited_message'];
        $message = [];

        foreach ($fields as $field) {
            if (isset($input[$field])) {
                $message = $input[$field];
                break;
            }
        }

        return $currentField ? Arr::get($message, $currentField) : $message;
    }
}

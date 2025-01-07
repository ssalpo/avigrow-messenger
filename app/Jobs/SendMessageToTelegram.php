<?php

namespace App\Jobs;

use App\Models\Account;
use App\Services\Avito;
use App\Services\DTO\Avito\AvitoWebhookPayloadDto;
use App\Services\Telegram;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int    $account,
        public array  $telegramIds,
        public string $chatId,
        public string $chatType,
        public AvitoWebhookPayloadDto  $payload,
    )
    {
        //
    }

    public function handle(): void
    {
        $account = Account::findOrFail($this->account);
        $contextMessage = Avito::getMessageBasedOnType($this->payload->type, $this->payload->content);
        $accountUrl = url("/accounts/{$account->id}/chats");

        $message = <<<MSG
<b>Аккаунт:</b> <a href="$accountUrl">{$account->name}</a> [{$account->id},{$this->chatId}]
<b>Контекст:</b> {$this->getContext($account)}
{$contextMessage}
MSG;

        Telegram::sendMessageToExistIds($message);
    }

    public function getContext(Account $account): string
    {
        $avito = (new Avito)->setAccount($account);

        $chat = $avito->getChatInfoById($this->chatId);

        if ($this->chatType === 'u2i') {
            return match ($chat->type) {
                'item' => $chat->item->title,
                default => '---'
            };
        }

        return Avito::getUserFromChat($chat->users, $account->external_id)->name;
    }
}

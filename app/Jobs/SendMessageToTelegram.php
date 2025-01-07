<?php

namespace App\Jobs;

use App\Models\Account;
use App\Services\Avito;
use App\Services\DTO\Avito\AvitoChatDto;
use App\Services\DTO\Avito\AvitoWebhookPayloadDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?AvitoChatDto $chat = null;

    private ?Avito $avito = null;

    private ?Account $account = null;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int                    $accountId,
        public array                  $telegramIds,
        public string                 $chatId,
        public string                 $chatType,
        public AvitoWebhookPayloadDto $payload,
    )
    {
        //
    }

    /**
     * @throws \Exception
     */
    public function handle(Avito $avito, Avito\Message\AvitoMessageHandlerRegistry $registry): void
    {
        if (!$this->payload->isSupportedContentType()) {
            return;
        }

        $this->account = Account::findOrFail($this->accountId);

        $this->avito = $avito->setAccount($this->account);

        $this->chat = $avito->getChatInfoById($this->chatId);

        $registry->getHandler($this->payload->type)->handle(
            $this->payload->content,
            $this->messageParams()
        );
    }

    private function messageParams(): array
    {
        $accountUrl = url("/accounts/{$this->account->id}/chats");
        $clientName = Avito::getUserFromChat($this->chat->users, $this->account->external_id)->name;
        $message = Avito::getMessageBasedOnType($this->payload);

        $params = [];

        if ($this->payload->isAds()) {
            $params['itemUrl'] = $this->chat->item->url;
            $params['itemTitle'] = $this->chat->item->title;
        }

        return array_merge([
            'accountId' => $this->account->id,
            'chatId' => $this->chatId,
            'accountUrl' => $accountUrl,
            'accountName' => $this->account->name,
            'price' => $this->chat->item->price,
            'clientName' => $clientName,
            'message' => $message,
        ], $params);
    }
}

<?php

namespace App\Jobs;

use App\Models\Account;
use App\Services\Avito;
use App\Services\Bot\ChatBot;
use App\Services\DTO\Avito\AvitoWebhookPayloadDto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleChatBotMessage implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected AvitoWebhookPayloadDto $payload,
        protected Account $account,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(Avito $avito): void
    {
        $message = $this->payload->content['text'];
        $chatId = $this->payload->chatId;
        $itemId = $this->payload->itemId;

        $chat = $avito->setAccount($this->account)->getChatInfoById($chatId);

        $user = Avito::getUserFromChat($chat->users, $this->account->id);

        $chatBot = new ChatBot();

        $placeholders = [
            '{name}' => $user->name,
            '{price}' => $chat->item->price,
            '{ad_title}' => $chat->item->title,
            '{location}' => $chat->item->location
        ];

        $chatBot->handleMessage(
            $this->account,
            $chatId,
            $itemId,
            $message,
            $placeholders
        );
    }
}

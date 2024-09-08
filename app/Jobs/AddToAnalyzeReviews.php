<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\AnalyzeReview;
use App\Services\Avito;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddToAnalyzeReviews implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int    $accountId,
        public string $chatId
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(Avito $avito): void
    {
        // Проверить существует ли в базе ранее добавленный элемент
        if (AnalyzeReview::where(['account_id' => $this->accountId, 'chat_id' => $this->chatId])->exists()) {
            return;
        }

        // Получить данные по аккаунту
        $account = Account::findOrFail($this->accountId);

        // Получить данные по чату
        $chatInfo = $avito->setAccount($account)->getChatInfoById($this->chatId);

        // Получить данные контекст и имя отправителя
        $contextId = $chatInfo['context']['value']['id'];
        $contextTitle = $chatInfo['context']['value']['title'];
        $senderName = collect($chatInfo['users'])->whereNotIn('id', [$account->external_id])->first()['name'];

        AnalyzeReview::create([
            'account_id' => $this->accountId,
            'chat_id' => $this->chatId,
            'context_id' => $contextId,
            'context' => $contextTitle,
            'chat_sender_name' => $senderName
        ]);
    }
}

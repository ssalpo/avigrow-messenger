<?php

namespace App\Jobs;

use App\Models\Account;
use App\Services\Avito;
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
        public array $payload,
    )
    {
        //
    }

    public function handle(): void
    {
        $account = Account::findOrFail($this->account);
        $contextMessage = Avito::getMessageBasedOnType($this->payload);

        $message = <<<MSG
<b>Аккаунт:</b> {$account->name}
<b>Контекст:</b> {$this->getContext($account)}
{$contextMessage}
MSG;

        foreach ($this->telegramIds as $telegramId) {
            $this->sendMessage($telegramId, $message);
        }
    }

    private function sendMessage(int $chatId, string $message): void
    {
        $apiToken = '6327522747:AAE0mb4LsWELuTN7GVX1h_f1ys8pVmHB35o';

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'html'
        ];

        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));
    }

    public function getContext(Account $account)
    {
        $avito = (new Avito)->setAccount($account);

        $chat = $avito->getChatInfoById($this->chatId);

        if ($this->chatType === 'u2i') {
            return match ($chat['context']['type']) {
                'item' => $chat['context']['value']['title'],
                default => '---'
            };
        }

        $user = collect($chat['users'])->whereNotIn('id', [$avito->me()['id']])->first();

        return $user['name'];
    }
}

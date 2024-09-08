<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\Avito;
use App\Services\Telegram;
use Illuminate\Console\Command;

class AnalyzeReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:analyze-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Анализирует сообщения и отзывы, чтобы найти тех, кто не оставил сообщения';

    /**
     * Execute the console command.
     */
    public function handle(Avito $avito)
    {
        $messages = "";

        $accounts = Account::with('analyzeReviews')->whereId(1)->get();

        $accounts->each(function (Account $account) use ($avito, &$messages) {
            $accountUrl = url("/accounts/{$account->id}/chats");

            $accountMessageTemplate = <<<MSG
<b>Аккаунт:</b> <a href="$accountUrl">{$account->name}</a>
=====

MSG;

            $avitoReviews = collect($avito->setAccount($account)->reviews()['reviews'])->map(function ($item) {
                return [
                    'itemId' => $item['item']['id'],
                    'senderName' => $item['sender']['name'],
                    'createdAt' => \Carbon\Carbon::createFromTimestamp($item['createdAt'])
                ];
            })->filter(fn($item) => now()->subDay()->toDateString() === $item['createdAt']->subDay()->toDateString());

            $hasAny = false;

            $account->analyzeReviews->each(function ($review) use ($avitoReviews, &$accountMessageTemplate, &$hasAny) {

                if(!$avitoReviews->where('itemId', $review['context_id'])->where('senderName', $review['chat_sender_name'])->count()) {
                    $hasAny = true;

                    $chatUrl = route('account.chat.messages', ['account' => $review->account_id, 'chat' => $review->chat_id]);

                    $chatItem = <<<CHMSG
<b>Чат:</b> <a href="$chatUrl">{$review['chat_sender_name']}</a>
<b>Контекст:</b> {$review['context']}
===========================

CHMSG;

                    $accountMessageTemplate .= $chatItem;

                    $review->delete();

                }
            });

            if($hasAny) {
                $messages .= $accountMessageTemplate;
            }
        });

        if($messages) {

            $messages = '<b>Анализатор отзывов</b> ' . PHP_EOL. PHP_EOL . $messages;

            Telegram::sendMessageToExistIds($messages);
        }
    }
}

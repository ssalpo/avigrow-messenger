<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\ReviewSchedule;
use App\Services\Avito;
use App\Services\Telegram;
use Illuminate\Console\Command;

class SendScheduledReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-scheduled-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправляет сообщения все пользователям, чтобы оставили отзыв';

    /**
     * Execute the console command.
     */
    public function handle(Avito $avito)
    {
        $reviews = ReviewSchedule::where('send_at', '<=', now())->get();
        $accounts = Account::whereIn('id', $reviews->pluck('account_id'))->get()->keyBy('id');
        $message = <<<MSG
Здравствуйте, прошел месяц с момента вашей покупки. Нам очень важно ваше мнение! Если у вас найдется пару минут, будем благодарны за отзыв.
MSG;

        $telegramMessage = <<<TMSG
Отправлен запрос на оставление отзыв.

<b>Чат:</b> <a href="%s">%s</a>
TMSG;


        foreach ($reviews as $review) {
            $account = $accounts[$review->account_id];

            try {
                $avito
                    ->setAccount($account)
                    ->sendMessage($review->chat_id, ['text' => $message]);

                $chatUrl = route('account.chat.messages', ['account' => $account->id, 'chat' => $review->chat_id]);

                $review->delete();

                Telegram::sendMessageToExistIds(sprintf($telegramMessage, $chatUrl, $account->name));
            } catch (\Exception $exception) {
                Telegram::sendMessageToExistIds(
                    sprintf('Ошибка при запросе отзыва. %s, %s, %s', json_encode($review->toArray()), json_encode($account->toArray()), $exception->getMessage())
                );
            }
        }
    }
}

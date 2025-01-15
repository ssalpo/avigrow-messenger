<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Review;
use App\Services\Avito;
use App\Services\GeminiService;
use App\Services\Telegram;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;

class ProcessReviewAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-review-answers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Автоматически отвечает на отзывы в Авито.';

    /**
     * Execute the console command.
     */
    public function handle(GeminiService $geminiService, Avito $avito)
    {
        $accounts = Account::where('can_answer_to_review', true)->get()->keyBy('id');

        $reviews = Review::query()
            ->select('reviews.*')
            ->joinSub(
                Review::query()
                    ->select('account_id', DB::raw('MAX(external_created_at) as latest_created_at'))
                    ->groupBy('account_id'),
                'latest_reviews',
                function ($join) {
                    $join->on('reviews.account_id', '=', 'latest_reviews.account_id')
                        ->on('reviews.external_created_at', '=', 'latest_reviews.latest_created_at');
                }
            )
            ->get();


        foreach ($reviews as $review) {
            $account = $accounts[$review->account_id];

            $avito->setAccount($account);

            // Получить ответ на отзыв из нейронки
            $answer = $geminiService->processReviewAnswer($review->item_title, $review->content);

            if ($answer === 'FALSE') {
                $review->delete();

                continue;
            }

            try {
                // Отправить отзыв на авито
                $avito->sendAnswerToReview($review->external_id, $answer);

                $review->delete();
            } catch (RequestException $e) {
                if ($e->response->json('error.code') === 'answer_already_exists') {
                    logger()?->info(json_encode(['deleted review', $review], JSON_THROW_ON_ERROR));

                    $review->delete();

                    continue;
                }

                throw new RequestException($e->response);
            }

            // Отправить уведомление в телеграм
            $msg = <<<MSG
📤 Отправлен ответ на отзыв

—————————————————
👤 Аккаунт: {$account->name}
—————————————————
💬 <b>Текст отзыва</b>: <i>{$review->content}</i>
—————————————————
✍️ <b>Ответ</b>: <i>$answer</i>
MSG;

            Telegram::sendMessageToExistIds($msg);
        }
    }
}

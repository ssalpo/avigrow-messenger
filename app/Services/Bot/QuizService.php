<?php

namespace App\Services\Bot;

use App\Models\Account;
use App\Models\Bot;
use App\Models\BotQuizState;
use App\Services\Avito;

class QuizService
{
    public function processAnswer(Account $account, Bot $bot, string $chatId, string $message, array $placeholders = []): void
    {
        if (!$bot->quizzes->count()) {
            return;
        }

        $state = BotQuizState::firstOrCreate([
            'bot_id' => $bot->id, 'chat_id' => $chatId
        ]);

        $contentToSend = null;

        if ($state->wasRecentlyCreated) {
            $contentToSend = $bot->quizzes[0]->content;
        } else {
            // Обработка всех ответов, после отправки первого вопроса квиза
            $currentQuiz = $bot->quizzes[$state->current_step];
            $nextQuestionIndex = $state->current_step + 1;

            $nextQuestion = $bot->quizzes[$nextQuestionIndex] ?? null;

            if ($currentQuiz->answer_type->isArbitrary()) {
                if ($nextQuestion) {
                    $contentToSend = $nextQuestion->content;

                    $state->update(['current_step' => $nextQuestionIndex]);
                } else {
                    $state->delete();
                }
            }

            if ($currentQuiz->answer_type->isOptions()) {
                if (in_array($message, $currentQuiz->options, true)) {
                    if ($nextQuestion) {
                        $contentToSend = $nextQuestion->content;
                        $state->update(['current_step' => $nextQuestionIndex]);
                    } else {
                        $state->delete();
                    }

                }
            }
        }

        if (!is_null($contentToSend)) {
            $messageToSend = PlaceholderService::replace(
                $contentToSend,
                $placeholders
            );

            (new Avito)->setAccount($account)->sendMessage($chatId, ['text' => $messageToSend]);
        }
    }
}

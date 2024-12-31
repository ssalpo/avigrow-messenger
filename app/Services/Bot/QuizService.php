<?php

namespace App\Services\Bot;

use App\Models\Account;
use App\Models\Bot;
use App\Models\BotQuiz;
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
            $quiz = $bot->quizzes[0];

            $contentToSend = $quiz->content;

            if($quiz->answer_type->isOptions()) {
                $contentToSend = $this->handleQuizOptions($quiz);
            }
        } else {
            // Обработка всех ответов, после отправки первого вопроса квиза
            $currentQuiz = $bot->quizzes[$state->current_step];
            $nextQuizIndex = $state->current_step + 1;

            $nextQuiz = $bot->quizzes[$nextQuizIndex] ?? null;

            $isLastStep = $nextQuizIndex === count($bot->quizzes);

            if ($currentQuiz->answer_type->isArbitrary() && $nextQuiz) {
                $contentToSend = $nextQuiz->content;

                if($nextQuiz->answer_type->isOptions()) {
                    $contentToSend = $this->handleQuizOptions($nextQuiz);
                }

                $state->update(['current_step' => $nextQuizIndex]);
            }

            if ($currentQuiz->answer_type->isOptions()) {
                if (in_array($message, $currentQuiz->options, true)) {
                    if ($nextQuiz) {
                        $contentToSend = $nextQuiz->content;

                        if($nextQuiz->answer_type->isOptions()) {
                            $contentToSend = $this->handleQuizOptions($nextQuiz);
                        }

                        $state->update(['current_step' => $nextQuizIndex]);
                    }
                } else {
                    $isLastStep = false;
                }
            }

            if ($isLastStep) {
                $state->delete();
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

    private function handleQuizOptions(BotQuiz $question): string
    {
        $optionsMSG = implode(PHP_EOL, $question->options);

        return <<<MSG
{$question->content}

Напишите один из вариантов ответа:
{$optionsMSG}
MSG;
    }
}

<?php

namespace App\Services\Bot;

use App\Models\Bot;
use App\Models\BotQuiz;
use App\Models\BotQuizState;

class QuizService
{
    public function processAnswer(Bot $bot, string $chatId, string $message, array $placeholders = []): void
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

            $nextQuestion = $bot->quizzes[$state->current_step + 1] ?? null;

            if ($currentQuiz->answer_type->isArbitrary()) {
                if ($nextQuestion) {
                    $contentToSend = $nextQuestion->content;

                    $state->update(['current_step' => $state->current_step + 1]);
                } else {
                    $state->delete();
                }
            }

            if ($currentQuiz->answer_type->isOptions()) {
                if (in_array($message, $currentQuiz->options, true)) {
                    if ($nextQuestion) {
                        $contentToSend = $nextQuestion->content;
                        $state->update(['current_step' => $state->current_step + 1]);
                    } else {
                        $state->delete();
                    }

                }
            }
        }

        if (!is_null($contentToSend)) {
            dd(PlaceholderService::replace(
                $contentToSend,
                $placeholders
            ));
        }
    }
}

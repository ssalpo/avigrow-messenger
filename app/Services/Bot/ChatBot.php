<?php

namespace App\Services\Bot;

use App\Jobs\SendMessageToAvito;
use App\Models\Account;
use App\Models\Ad;
use App\Models\Bot;
use App\Models\BotChatState;
use App\Services\Avito;

class ChatBot
{
    protected function getCurrentBot(Account $account, string $itemId): ?Bot
    {
        $ad = Ad::where(
            ['account_id' => $account->id, 'external_id' => $itemId]
        )->first();

        return !is_null($ad) ? $ad : $account->bot;
    }

    public function handleMessage(Account $account, string $chatId, string $message, array $placeholders = []): void
    {
        if($chatId !== 'u2i-1R3VAs3R1anQIg2Quloymw') {
            return;
        }

        $bot = $this->getCurrentBot($account, $chatId);

        if(!$bot || ($bot && !$bot->is_active)) return;

        if($bot->type->isStandard()) {
            $bot->load(['greetings', 'triggers']);

            $chatState = BotChatState::firstOrCreate(['account_id' => $account->id, 'chat_id' => $chatId]);

            $isGreeted = $chatState->greeted;

            if(!$isGreeted) {
                $this->handleGreeting($account, $chatId, $bot, $chatState, $placeholders);
            }

            if ($isGreeted && $bot->triggers->count() && $bot->type->isStandard()) {
                $this->handleTriggers($account, $chatId, $bot, $message, $placeholders);
            }
        }

        if($bot->type->isQuiz()) {
            $bot->load(['quizzes']);

            (new QuizService())->processAnswer(
                $account, $bot, $chatId, $message, $placeholders
            );
        }
    }

    private function handleGreeting(Account $account, string $chatId, Bot $bot, BotChatState $chatState, array $placeholders): void
    {
        $greetingManager = new GreetingManager();

        foreach ($bot->greetings as $greeting) {
            $greetingManager->addGreeting($greeting);
        }

        $greeting = $greetingManager->getRandomGreeting();

        $chatState->update(['greeted' => true, 'chat_id' => $chatId]);

        $messageToSend = PlaceholderService::replace(
            $greeting->template,
            $placeholders
        );

        if($messageToSend) {
            if($greeting->delay > 0) {
                SendMessageToAvito::dispatch(
                    $account, $chatId, $messageToSend
                )->delay(now()->addSeconds($greeting->delay));

                return;
            }

            (new Avito)->setAccount($account)->sendMessage($chatId, ['text' => $messageToSend]);
        }
    }

    private function handleTriggers(Account $account, string $chatId, Bot $bot, string $message, array $placeholders): void
    {
        $triggerManager = new TriggerManager();

        foreach ($bot->triggers as $trigger) {
            $triggerManager->addTrigger($trigger);
        }

        $trigger = $triggerManager->findMatchingTrigger($message);

        if ($trigger) {
            $messageToSend = PlaceholderService::replace(
                $trigger->response,
                $placeholders
            );

            if($trigger->delay > 0) {
                SendMessageToAvito::dispatch(
                    $account, $chatId, $messageToSend
                )->delay(now()->addSeconds($trigger->delay));

                return;
            }

            (new Avito)->setAccount($account)->sendMessage($chatId, ['text' => $messageToSend]);
        }
    }
}

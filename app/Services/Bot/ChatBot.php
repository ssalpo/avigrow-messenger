<?php

namespace App\Services\Bot;

use App\Jobs\TestChatBotMessageSend;
use App\Models\Account;
use App\Models\Bot;
use App\Models\BotChatState;
use App\Models\ConversationBot;
use App\Services\Avito;

class ChatBot
{
    protected function getCurrentBot(Account $account, string $chatId): ?Bot
    {
        $conversation = ConversationBot::where(
            ['account_id' => $account->id, 'chat_id' => $chatId]
        )->first();

        if ($conversation) {
            return $conversation->bot;
        }

        return $account->bot;
    }

    public function handleMessage(Account $account, string $chatId, string $message, array $placeholders = []): void
    {
        $bot = $this->getCurrentBot($account, $chatId);

        if(!$bot || ($bot && !$bot->is_active)) return;

        if($bot->type->isStandard()) {
            $bot->load(['greetings', 'triggers']);

            $chatState = BotChatState::firstOrCreate(['account_id' => $account->id, 'chat_id' => $chatId]);

            $isGreeted = $chatState->greeted;

            if(!$isGreeted) {
                $this->handleGreeting($chatId, $bot, $chatState, $placeholders);
            }

            if ($isGreeted && $bot->triggers->count() && $bot->type->isStandard()) {
                $this->handleTriggers($bot, $message, $placeholders);
            }
        }

        if($bot->type->isQuiz()) {
            $bot->load(['quizzes']);

            (new QuizService())->processAnswer(
                $bot, $chatId, $message, $placeholders
            );
        }
    }

    private function handleGreeting(string $chatId, Bot $bot, BotChatState $chatState, array $placeholders): void
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
                TestChatBotMessageSend::dispatch($messageToSend)->delay(now()->addSeconds($greeting->delay));
                return;
            }

            dd($messageToSend);
        }
    }

    private function handleTriggers(Bot $bot, string $message, array $placeholders): void
    {
        $triggerManager = new TriggerManager();

        foreach ($bot->triggers as $trigger) {
            $triggerManager->addTrigger($trigger);
        }

        $trigger = $triggerManager->findMatchingTrigger($message);

        if ($trigger) {
            $messageToSend = PlaceholderService::replace(
                $trigger->getResponse($placeholders),
                $placeholders
            );

            if($trigger->delay > 0) {
                TestChatBotMessageSend::dispatch($messageToSend)->delay(now()->addSeconds($trigger->delay));
                return;
            }

            dd($messageToSend);
        }
    }

}

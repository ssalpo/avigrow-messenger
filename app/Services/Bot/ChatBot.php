<?php

namespace App\Services\Bot;

use App\Models\Account;
use App\Models\Bot;
use App\Models\BotChatState;
use App\Models\ConversationBot;

class ChatBot
{
    protected function getCurrentBot(Account $account, string $chatId)
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

        if(!$bot->is_active) return;

        $bot->load(['greetings', 'triggers']);

        $chatState = BotChatState::firstOrCreate(['account_id' => $account->id, 'chat_id' => $chatId]);

        $this->handleGreeting($chatId, $bot, $chatState, $placeholders);

        if ($bot->type->isStandard()) {
            $this->handleTriggers($bot, $message, $chatState, $placeholders);
        }
    }

    private function handleGreeting(string $chatId, Bot $bot, BotChatState $chatState, array $placeholders): void
    {
        if ($chatState->greeted || !$bot->greetings->count()) {
            return;
        }

        $greetingManager = new GreetingManager();

        foreach ($bot->greetings as $greeting) {
            $greetingManager->addGreeting($greeting);
        }

        $greeting = $greetingManager->getRandomGreeting();

        $chatState->update(['greeted' => true, 'chat_id' => $chatId]);

        $textToSend = PlaceholderService::replace(
            $greeting->template,
            $placeholders
        );

        dd($textToSend);
    }

    private function handleTriggers(Bot $bot, string $message, BotChatState $chatState, array $placeholders): void
    {
        if (!$chatState->greeted || !$bot->triggers->count()) {
            return;
        }

        $triggerManager = new TriggerManager();

        foreach ($bot->triggers as $trigger) {
            $triggerManager->addTrigger($trigger);
        }


        $trigger = $triggerManager->findMatchingTrigger($message);

        if ($trigger) {
            $textToSend = PlaceholderService::replace(
                $trigger->getResponse($placeholders),
                $placeholders
            );

            dd($textToSend);
        }
    }

}

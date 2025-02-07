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
    protected function getCurrentBot(Account $account, ?string $itemId): ?Bot
    {
        $adBot = Ad::where(
            ['account_id' => $account->id, 'external_id' => $itemId]
        )->first()?->bot;

        return !is_null($adBot) ? $adBot : $account->bot;
    }

    public function handleMessage(Account $account, string $chatId, ?string $itemId, string $message, array $placeholders = []): void
    {
        $bot = $this->getCurrentBot($account, $itemId);

        if (
            !$bot ||
            !$bot->is_active ||
            !BotScheduleService::isInSchedule($bot->id, $account->timezone)
        ) {
            return;
        }

        if ($bot->type->isStandard()) {
            $bot->load(['greetings', 'triggers']);

            $chatState = BotChatState::firstOrCreate(['account_id' => $account->id, 'chat_id' => $chatId]);

            $isGreeted = $chatState->greeted;

            if (!$isGreeted) {
                $this->handleGreeting($account, $chatId, $bot, $chatState, $placeholders);
            }

            if ($isGreeted && $bot->triggers->count() && $bot->type->isStandard()) {
                $this->handleTriggers($account, $chatId, $bot, $message, $placeholders);
            }
        }

        if ($bot->type->isQuiz()) {
            $bot->load(['quizzes' => fn($q) => $q->orderBy('sort')]);

            $answer = (new QuizService())->processAnswer(
                $account, $bot, $chatId, $message, $placeholders
            );

            if (!is_null($answer)) {
                $this->sendMessage($account, $bot, $chatId, $message);
            }
        }
    }

    private function handleGreeting(Account $account, string $chatId, Bot $bot, BotChatState $chatState, array $placeholders): void
    {
        $greetingManager = new GreetingManager();

        foreach ($bot->greetings as $greeting) {
            $greetingManager->addGreeting($greeting);
        }

        $greeting = $greetingManager->getRandomGreeting();

        $from = now($account->timezone)->setTimeFrom($greeting->schedule_from);
        $to = now($account->timezone)->setTimeFrom($greeting->schedule_to);

        if(!now($account->timezone)->between($from, $to)) {
            return;
        }

        $chatState->update(['greeted' => true, 'chat_id' => $chatId]);

        $messageToSend = PlaceholderService::replace(
            $greeting->template,
            $placeholders,
            $account->timezone
        );

        if ($messageToSend) {
            if ($greeting->delay > 0) {
                SendMessageToAvito::dispatch(
                    $account, $chatId, $messageToSend
                )->delay(now()->addSeconds($greeting->delay));

                return;
            }

            $this->sendMessage($account, $bot, $chatId, $messageToSend);
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
                $placeholders,
                $account->timezone
            );

            if ($trigger->delay > 0) {
                SendMessageToAvito::dispatch(
                    $account, $chatId, $messageToSend
                )->delay(now()->addSeconds($trigger->delay));

                return;
            }

            $this->sendMessage($account, $bot, $chatId, $messageToSend);
        }
    }

    private function sendMessage(Account $account, Bot $bot, string $chatId, string $message): void
    {
        $avito = (new Avito)->setAccount($account);

        $avito->sendMessage($chatId, ['text' => $message]);

        if(!$bot->mark_chat_as_unread) {
            $avito->markChatAsRead($chatId);
        }
    }
}

<?php

namespace App\Services\Bot;

use App\Models\BotTrigger;

class TriggerManager
{
    private array $triggers = [];

    public function addTrigger(BotTrigger $trigger): void
    {
        $this->triggers[] = $trigger;
    }

    public function findMatchingTrigger(string $text): ?BotTrigger
    {
        foreach ($this->triggers as $trigger) {
            if ($trigger->matches($text)) {
                return $trigger;
            }
        }

        return null;
    }
}

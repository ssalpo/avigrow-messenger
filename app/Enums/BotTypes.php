<?php

namespace App\Enums;

enum BotTypes: int
{
    case STANDARD = 1;

    case QUIZ = 2;

    public function isStandard(): bool
    {
        return $this->value === self::STANDARD->value;
    }

    public function isQuiz(): bool {
        return $this->value === self::QUIZ->value;
    }
}

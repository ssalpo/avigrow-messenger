<?php

namespace App\Enums;

use App\Enums\Traits\EnumToCollection;

enum BotTypes: int
{
    use EnumToCollection;

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

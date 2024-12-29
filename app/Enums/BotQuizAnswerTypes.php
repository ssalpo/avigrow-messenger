<?php

namespace App\Enums;

use App\Enums\Traits\EnumToCollection;

enum BotQuizAnswerTypes: int
{
    use EnumToCollection;

    case ARBITRARY = 1;

    case OPTIONS = 2;

    public function isArbitrary(): bool
    {
        return $this->value === self::ARBITRARY->value;
    }

    public function isOptions(): bool {
        return $this->value === self::OPTIONS->value;
    }
}

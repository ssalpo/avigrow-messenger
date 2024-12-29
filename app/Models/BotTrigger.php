<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotTrigger extends Model
{
    protected $fillable = [
        'bot_id',
        'keywords',
        'response',
        'case_sensitive',
        'delay'
    ];

    protected $casts = [
        'keywords' => 'array',
        'case_sensitive' => 'boolean'
    ];

    public function matches(string $text): bool
    {
        $keywords = $this->keywords;
        $mode = null;

        // Нормализация текста для нечувствительного к регистру поиска
        if (!$this->case_sensitive) {
            $text = mb_strtolower($text);
            $keywords = array_map('mb_strtolower', $keywords);
        }

        foreach ($keywords as $keyword) {
            switch ($mode) {
                case 'contains':
                    // Триггеры: ключевые слова содержатся где-либо в тексте
                    if ($this->case_sensitive) {
                        if (str_contains($text, $keyword)) {
                            return true;
                        }
                    } else if (stripos($text, $keyword) !== false) {
                        return true;
                    }
                    break;

                default:
                    // Точное совпадение
                    if ($this->case_sensitive) {
                        if ($text === $keyword) {
                            return true;
                        }
                    } else if (strcasecmp($text, $keyword) === 0) {
                        return true;
                    }
            }
        }

        return false;
    }
}

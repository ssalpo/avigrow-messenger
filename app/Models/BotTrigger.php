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
        foreach ($this->keywords as $keyword) {
            if ($this->case_sensitive) {
                if (strpos($text, $keyword) !== false) {
                    return true;
                }
            } else {
                if (stripos($text, $keyword) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getResponse(array $placeholders = []): string
    {
        return str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $this->response
        );
    }
}

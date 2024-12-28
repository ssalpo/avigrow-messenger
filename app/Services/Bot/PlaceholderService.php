<?php

namespace App\Services\Bot;

class PlaceholderService
{
    /**
     * Заменяет макросы в приветствии.
     *
     * @param string $template
     * @param array $placeholders
     * @return string
     */
    public static function replace(string $template, array $placeholders): string
    {
        // Добавляем макрос приветствия по времени
        $placeholders['{time_greeting}'] = self::getTimeGreeting();

        foreach ($placeholders as $key => $value) {
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }

    /**
     * Возвращает приветствие по времени суток.
     *
     * @return string
     */
    public static function getTimeGreeting(): string
    {
        $hour = (int)date('H');

        if ($hour < 12) {
            return 'Доброе утро';
        } elseif ($hour < 18) {
            return 'Добрый день';
        }

        return 'Добрый вечер';
    }
}

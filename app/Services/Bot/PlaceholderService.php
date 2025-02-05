<?php

namespace App\Services\Bot;

class PlaceholderService
{
    /**
     * Заменяет макросы в приветствии.
     *
     * @param string $template
     * @param array $placeholders
     * @param string|null $timezone
     * @return string
     */
    public static function replace(string $template, array $placeholders, string $timezone = null): string
    {
        // Добавляем макрос приветствия по времени
        $placeholders['{welcome}'] = self::getTimeGreeting($timezone);

        foreach ($placeholders as $key => $value) {
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }

    /**
     * Возвращает приветствие по времени суток.
     *
     * @param string|null $timezone
     * @return string
     */
    public static function getTimeGreeting(string $timezone = null): string
    {
        $hour = (int) now($timezone)->format('H');

        if ($hour < 12) {
            return 'Доброе утро';
        } elseif ($hour < 18) {
            return 'Добрый день';
        }

        return 'Добрый вечер';
    }
}

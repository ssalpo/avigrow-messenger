<?php

namespace App\Services\Bot;

use App\Models\BotGreeting;

class GreetingManager
{
    private array $greetings;

    public function __construct(array $greetings = [])
    {
        $this->greetings = $greetings;
    }

    /**
     * Возвращает случайное приветствие с заменой макросов.
     *
     * @return BotGreeting
     */
    public function getRandomGreeting(): BotGreeting
    {
        return $this->greetings[array_rand($this->greetings)];
    }

    /**
     * Добавляет новое приветствие в список.
     *
     * @param BotGreeting $greeting
     * @return void
     */
    public function addGreeting(BotGreeting $greeting): void
    {
        $this->greetings[] = $greeting;
    }

    /**
     * Возвращает все доступные приветствия.
     *
     * @return array
     */
    public function getAllGreetings(): array
    {
        return $this->greetings;
    }
}

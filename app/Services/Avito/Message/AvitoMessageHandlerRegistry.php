<?php

namespace App\Services\Avito\Message;

use App\Services\Avito\Message\Handlers\AvitoMessageHandler;
use Exception;

class AvitoMessageHandlerRegistry
{
    private string $namespace;

    private array $handlers = [];

    public function __construct(string $namespace = 'App\\Services\\Avito\\Message\\Handlers')
    {
        $this->namespace = $namespace;
    }


    public function getHandler(string $type): AvitoMessageHandler
    {
        // Проверяем, есть ли уже обработчик в реестре
        if (!isset($this->handlers[$type])) {
            // Формируем полное имя класса из namespace и типа
            $className = $this->namespace . '\\' .'AvitoMessage' . ucfirst($type) . 'Handler';

            // Проверяем, существует ли класс
            if (!class_exists($className)) {
                throw new Exception("Обработчик для типа '$type' не найден");
            }

            // Проверяем, реализует ли класс интерфейс MessageHandler
            if (!is_subclass_of($className, AvitoMessageHandler::class)) {
                throw new Exception("Класс '$className' не является обработчиком сообщений");
            }

            // Создаем и сохраняем обработчик
            $this->handlers[$type] = new $className();
        }

        return $this->handlers[$type];
    }
}

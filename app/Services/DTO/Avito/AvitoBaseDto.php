<?php

namespace App\Services\DTO\Avito;

abstract class AvitoBaseDto
{
    public static array $raw;

    public function getRaw(): array
    {
        return static::$raw;
    }

    public static function setRaw(array $data): void {
        static::$raw = $data;
    }

    abstract public static function fromArray(array $data): self;
}

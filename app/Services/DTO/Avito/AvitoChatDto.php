<?php

namespace App\Services\DTO\Avito;

class AvitoChatDto extends AvitoBaseDto
{
    public function __construct(
        public readonly string $id,
        public readonly AvitoItemDto $item,
        public readonly array $users,
        public readonly AvitoChatLastMessageDto $lastMessage,
        public readonly string $type
    )
    {
    }

    public static function fromArray(array $data): AvitoChatDto
    {
        static::setRaw($data);

        return new self(
            $data['id'],
            AvitoItemDto::fromArray($data),
            $data['users'],
            AvitoChatLastMessageDto::fromArray($data['last_message']),
            $data['context']['type']
        );
    }
}

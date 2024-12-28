<?php

namespace App\Services\DTO\Avito;

class AvitoChatLastMessageDto extends AvitoBaseDto
{
    public function __construct(
        public readonly string $id,
        public readonly int    $authorId,
        public readonly array  $content,
        public readonly int    $created,
        public readonly bool   $isRead,
        public readonly string $type,
    )
    {
    }

    public static function fromArray(array $data): AvitoChatLastMessageDto
    {
        static::setRaw($data);

        return new self(
            $data['id'],
            $data['author_id'],
            $data['content'],
            $data['created'],
            isset($chat['read']),
            $data['type'],
        );
    }
}

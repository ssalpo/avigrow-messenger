<?php

namespace App\Services\DTO\Avito;

use Carbon\Carbon;

class AvitoWebhookPayloadDto extends AvitoBaseDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $chatId,
        public readonly int $userId,
        public readonly int $authorId,
        public readonly int $created,
        public readonly string $type,
        public readonly string $chatType,
        public readonly array $content,
        public readonly int $itemId,
        public readonly string $publishedAt,
        public readonly bool $isRead
    )
    {
    }

    public static function fromArray(array $data): AvitoWebhookPayloadDto
    {
        return new self(
            $data['value']['id'],
            $data['value']['chat_id'],
            $data['value']['user_id'],
            $data['value']['author_id'],
            $data['value']['created'],
            $data['value']['type'],
            $data['value']['chat_type'],
            $data['value']['content'],
            $data['value']['item_id'],
            $data['value']['published_at'],
            isset($payload['value']['read'])
        );
    }

    public function createdAtFormated(string $format = 'Y.m.d, H:i'): string
    {
        return Carbon::createFromTimestamp($this->created)->format($format);
    }
}

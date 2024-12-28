<?php
namespace App\Services\DTO\Avito;

class AvitoItemDto extends AvitoBaseDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $price,
        public readonly string $url,
        public readonly string $location,
    )
    {
    }

    public static function fromArray(array $data): AvitoItemDto
    {
        static::setRaw($data);

        $item = $data['context']['value'];

        return new self(
            $item['id'],
            $item['title'],
            self::parsePrice($item['price_string']),
            $item['url'],
            $item['location']['title'],
        );
    }

    public static function parsePrice(string $price): string
    {
        return str_replace(' ₽', '', $price);
    }
}

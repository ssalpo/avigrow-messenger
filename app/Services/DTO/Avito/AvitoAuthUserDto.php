<?php

namespace App\Services\DTO\Avito;

class AvitoAuthUserDto extends AvitoBaseDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $profileUrl,
    )
    {
    }

    public static function fromArray(array $data): AvitoAuthUserDto
    {
        static::setRaw($data);

        return new self(
            $data['id'],
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['profile_url'],
        );
    }
}

<?php

namespace App\Services\DTO\Avito;

class AvitoChatUserDto extends AvitoBaseDto
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $avatar,
        public readonly array  $avatarOthers,
        public readonly string $profileUrl,
    )
    {
    }

    public static function fromArray(array $data): AvitoChatUserDto
    {
        static::setRaw($data);

        return new self(
            $data['id'],
            $data['name'],
            $data['public_user_profile']['avatar']['default'],
            $data['public_user_profile']['avatar']['images'],
            $data['public_user_profile']['url'],
        );
    }
}

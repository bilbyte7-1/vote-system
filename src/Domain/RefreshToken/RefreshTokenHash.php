<?php

namespace App\Domain\RefreshToken;

final readonly class RefreshTokenHash
{
    public function __construct(
        private string $hash,
    ) {
        if ($hash === '') {
            throw new \InvalidArgumentException('Refresh token hash cannot be empty.');
        }
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}

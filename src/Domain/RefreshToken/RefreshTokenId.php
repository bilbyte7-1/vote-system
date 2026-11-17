<?php

namespace App\Domain\RefreshToken;

use InvalidArgumentException;

class RefreshTokenId
{
    public function __construct(private string $id)
    {
        if ($id === '') {
            throw new InvalidArgumentException('RefreshTokenId cannot be empty.');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}

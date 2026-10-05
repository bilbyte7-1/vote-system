<?php

namespace App\Domain\RefreshToken;

use InvalidArgumentException;

class RefreshTokenId
{
    public function __construct(private string $id){
        if ($id === '') {
            throw new InvalidArgumentException('UserId cannot be empty.');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }
}

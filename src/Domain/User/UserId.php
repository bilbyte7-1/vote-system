<?php

namespace App\Domain\User;

use InvalidArgumentException;

final readonly class UserId
{
    public function __construct(
        private string $id,
    ) {
        if ($id === '') {
            throw new InvalidArgumentException('UserId cannot be empty.');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }
}

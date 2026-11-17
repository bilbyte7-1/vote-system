<?php

namespace App\Domain\User;

class Password
{
    public function __construct(
        private string $passwordHash
    ) {
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}

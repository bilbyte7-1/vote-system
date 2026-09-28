<?php

namespace App\Domain\User;

use InvalidArgumentException;

final readonly class Email
{
    private string $email;

    public function __construct(string $email)
    {
        $email = mb_strtolower(trim($email));

        if ($email === '') {
            throw new InvalidArgumentException('Email cannot be empty.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                sprintf('"%s" is not a valid email.', $email)
            );
        }

        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

<?php

namespace App\Application\Registration;

final readonly class RegisterUserRequest
{
    public function __construct(
        public string $email,
        public string $plainPassword,
    ) {
    }
}

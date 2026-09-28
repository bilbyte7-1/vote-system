<?php

namespace App\Application\Authentication;

final readonly class AuthenticationUserRequest
{
    public function __construct(
        public string $email,
        public string $plainPassword,
    ) {
    }
}

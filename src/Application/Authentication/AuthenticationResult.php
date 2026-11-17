<?php

namespace App\Application\Authentication;

final readonly class AuthenticationResult
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken
    ) {
    }
}

<?php

namespace App\Application\Authentication;

class AuthenticationResult
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken
    ) {
    }
}

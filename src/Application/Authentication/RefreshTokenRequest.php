<?php

namespace App\Application\Authentication;

class RefreshTokenRequest
{
    public function __construct(
        public string $refreshToken,
    ) {
    }
}

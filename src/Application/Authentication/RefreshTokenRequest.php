<?php

namespace App\Application\Authentication;

final readonly class RefreshTokenRequest
{
    public function __construct(
        public string $refreshToken,
    ) {
    }
}

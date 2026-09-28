<?php

namespace App\Infrastructure\Security\RefreshToken;

use App\Application\Interfaces\RefreshTokenHasherInterface;

final class Sha256RefreshTokenHasher implements RefreshTokenHasherInterface
{
    public function hash(string $refreshToken): string
    {
        return hash('sha256', $refreshToken);
    }
}

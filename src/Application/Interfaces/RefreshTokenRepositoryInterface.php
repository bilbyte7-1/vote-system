<?php

namespace App\Application\Interfaces;

use App\Domain\RefreshToken\RefreshToken;

interface RefreshTokenRepositoryInterface
{
    public function save(RefreshToken $refreshToken): void;

    public function findByHash(string $hash): ?RefreshToken;

    public function rotate(
        RefreshToken $refreshToken,
        RefreshToken $newRefreshToken
    ): void;
}

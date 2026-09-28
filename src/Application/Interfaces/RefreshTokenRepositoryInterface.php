<?php

namespace App\Application\Interfaces;

use App\Domain\User\RefreshToken\RefreshToken;

interface RefreshTokenRepositoryInterface
{
    public function save(RefreshToken $refreshToken): void;

    public function findByHash(string $hash): ?RefreshToken;

    public function rotate(
        RefreshToken $refreshToken,
        RefreshToken $newRefreshToken
    ): void;
}

<?php

namespace App\Tests\Fakers;

use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Domain\RefreshToken\RefreshToken;
use LogicException;

class InMemoryRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(
        private ?RefreshToken $savedRefreshToken = null,
    ) {
    }

    public function save(RefreshToken $refreshToken): void
    {
        $this->savedRefreshToken = $refreshToken;
    }

    public function findByHash(string $hash): ?RefreshToken
    {
        return $this->savedRefreshToken?->getTokenHash()->getHash() === $hash
            ? $this->savedRefreshToken
            : null;
    }

    public function getSavedRefreshToken(): RefreshToken
    {
        return $this->savedRefreshToken
            ?? throw new LogicException('No refresh token has been saved.');
    }
    public function rotate(
        RefreshToken $refreshToken,
        RefreshToken $newRefreshToken
    ): void {
        if ($this->savedRefreshToken !== $refreshToken) {
            throw new LogicException('Refresh token not found.');
        }

        $this->savedRefreshToken = $newRefreshToken;
    }
}

<?php

namespace App\Infrastructure\Security\RefreshToken;

use App\Application\Interfaces\RefreshTokenHasherInterface;
use App\Application\Interfaces\RefreshTokenVerifierInterface;
use App\Domain\RefreshToken\RefreshToken;
use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Infrastructure\Exceptions\RefreshTokenExpiredException;
use App\Infrastructure\Exceptions\RefreshTokenNotFoundException;
use App\Infrastructure\Exceptions\RefreshTokenRevokedException;
use Psr\Clock\ClockInterface;

class RefreshTokenVerifier implements RefreshTokenVerifierInterface
{
    public function __construct(
        private RefreshTokenHasherInterface $refreshTokenHasher,
        private RefreshTokenRepositoryInterface $refreshTokenRepository,
        private ClockInterface $clock,
    ) {
    }

    public function verify(string $rawRefreshToken): RefreshToken
    {
        $tokenHash = $this->refreshTokenHasher->hash($rawRefreshToken);

        $refreshToken = $this->refreshTokenRepository->findByHash($tokenHash);

        if ($refreshToken === null) {
            throw new RefreshTokenNotFoundException();
        }

        if ($refreshToken->isExpired($this->clock->now())) {
            throw new RefreshTokenExpiredException();
        }

        if ($refreshToken->isRevoked()) {
            throw new RefreshTokenRevokedException();
        }

        return $refreshToken;
    }
}

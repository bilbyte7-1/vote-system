<?php

namespace App\Infrastructure\Security\RefreshToken;

use App\Application\Interfaces\RefreshTokenHasherInterface;
use App\Application\Interfaces\RefreshTokenVerifierInterface;
use App\Domain\User\RefreshToken\RefreshToken;
use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Infrastructure\Exceptions\RefreshTokenExpiredException;
use App\Infrastructure\Exceptions\RefreshTokenNotFoundException;
use Psr\Clock\ClockInterface;

class RefreshTokenVerifier implements RefreshTokenVerifierInterface
{
    public function __construct(
        private RefreshTokenHasherInterface $refreshTokenHasher,
        private RefreshTokenRepositoryInterface $refreshTokenRepository,
        private ClockInterface $clock,
    ){}

    public function verify(string $rawRefreshToken): Refreshtoken
    {
        $tokenHash = $this->refreshTokenHasher->hash($rawRefreshToken);

        $refreshToken = $this->refreshTokenRepository->findByHash($tokenHash);

        if ($refreshToken === null) {
            throw new RefreshTokenNotFoundException();
        }

        if ($refreshToken->isExpired($this->clock->now())) {
            throw new RefreshTokenExpiredException();
        }

        return $refreshToken;
    }
}

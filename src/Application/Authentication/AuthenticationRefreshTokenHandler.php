<?php

namespace App\Application\Authentication;

use App\Application\Exceptions\UserNotFoundException;
use App\Application\Interfaces\AccessTokenIssuerInterface;
use App\Application\Interfaces\RefreshTokenIssuerInterface;
use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Application\Interfaces\RefreshTokenVerifierInterface;
use App\Application\Interfaces\UserRepositoryInterface;
use App\Application\RefreshToken\RefreshTokenInitializer;
use Psr\Clock\ClockInterface;

class AuthenticationRefreshTokenHandler
{
    public function __construct(
        private RefreshTokenVerifierInterface $refreshTokenVerifier,
        private UserRepositoryInterface $userRepository,
        private AccessTokenIssuerInterface $accessTokenIssuer,
        private RefreshTokenIssuerInterface $refreshTokenIssuer,
        private RefreshTokenInitializer $refreshTokenInitializer,
        private RefreshTokenRepositoryInterface $refreshTokenRepository,
        private ClockInterface $clock,
    ) {
    }

    public function handle(RefreshTokenRequest $refreshTokenRequest): AuthenticationResult
    {
        $refreshToken = $this->refreshTokenVerifier->verify($refreshTokenRequest->refreshToken);
        $user = $this->userRepository->findById(
            $refreshToken->getUserId()
        );

        if (null === $user) {
            throw new UserNotFoundException();
        }
        $rawRefreshToken = $this->refreshTokenIssuer->issue();
        $newRefreshToken = $this->refreshTokenInitializer->initialize($user->getId(), $rawRefreshToken);
        $accessToken = $this->accessTokenIssuer->issue($user);

        $refreshToken->revoke($this->clock->now());
        $this->refreshTokenRepository->rotate($refreshToken, $newRefreshToken);

        return new AuthenticationResult(
            $accessToken,
            $rawRefreshToken,
        );
    }
}

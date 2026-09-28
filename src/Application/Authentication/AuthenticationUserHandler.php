<?php

namespace App\Application\Authentication;

use App\Application\Interfaces\AccessTokenIssuerInterface;
use App\Application\Interfaces\AuthenticationProviderInterface;
use App\Application\Interfaces\RefreshTokenIssuerInterface;
use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Application\RefreshToken\RefreshTokenInitializer;

readonly class AuthenticationUserHandler
{
    public function __construct(
        private readonly AuthenticationProviderInterface  $authenticationProvider,
        private readonly AccessTokenIssuerInterface       $accessTokenIssuer,
        private readonly RefreshTokenIssuerInterface      $refreshTokenIssuer,
        private readonly RefreshTokenInitializer          $refreshTokenInitializer,
        private readonly  RefreshTokenRepositoryInterface $refreshTokenRepository,
    ) {
    }

    public function handle( AuthenticationUserRequest $request ): AuthenticationResult
    {
        $user = $this->authenticationProvider->authenticate(
            $request->email,
            $request->plainPassword,
        );

        $accessToken = $this->accessTokenIssuer->issue($user);

        $rawRefreshToken = $this->refreshTokenIssuer->issue();

        $refreshToken = $this->refreshTokenInitializer->initialize(
            $user->getId(),
            $rawRefreshToken
        );

        $this->refreshTokenRepository->save($refreshToken);
        return new AuthenticationResult($accessToken, $rawRefreshToken);
    }
}

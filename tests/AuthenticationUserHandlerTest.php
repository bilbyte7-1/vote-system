<?php

namespace App\Tests;

use App\Application\Authentication\AuthenticationUserHandler;
use App\Application\Authentication\AuthenticationUserRequest;
use App\Application\Interfaces\AccessTokenIssuerInterface;
use App\Application\Interfaces\AuthenticationProviderInterface;
use App\Application\Interfaces\RefreshTokenIssuerInterface;
use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Application\RefreshToken\RefreshTokenInitializer;
use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\RefreshToken\RefreshToken;
use App\Domain\User\User;
use App\Domain\User\UserId;
use PHPUnit\Framework\TestCase;
use App\Domain\User\RefreshToken\RefreshTokenId;
use App\Domain\User\RefreshToken\RefreshTokenHash;


class AuthenticationUserHandlerTest extends TestCase
{
    public function testItAuthenticationUser(): void
    {
        $user = new User(
            new UserId('user-id'),
            new Email('user@example.com'),
            new Password('hashed-password'),
        );

        $refreshToken = new RefreshToken(
            new RefreshTokenId('refresh-token-id'),
            $user->getId(),
            new RefreshTokenHash('refresh-token-hash'),
            new \DateTimeImmutable('2026-09-16 12:00:00'),
            new \DateTimeImmutable('2026-10-16 12:00:00'),
            null
        );

        $request = new AuthenticationUserRequest(
            email: 'user@example.com',
            plainPassword: 'password123',
        );

        $mockAuthenticationProvider = $this->createMock(AuthenticationProviderInterface::class);
        $mockAuthenticationProvider
            ->expects($this->once())
            ->method('authenticate')
            ->with(
                $request->email,
                $request->plainPassword,
            )
            ->willReturn($user);

        $stubAccessTokenIssuer = $this->createStub(AccessTokenIssuerInterface::class);
        $stubAccessTokenIssuer
            ->method('issue')
            ->willReturn('fake-access-token');
        $stubRefreshTokenIssuer = $this->createStub(RefreshTokenIssuerInterface::class);
        $stubRefreshTokenIssuer
            ->method('issue')
            ->willReturn('refresh-token');

        $mockRefreshTokenInitializer = $this->createMock(RefreshTokenInitializer::class);
        $mockRefreshTokenInitializer
            ->expects(self::once())
            ->method('initialize')
            ->with(
                $user->getId(),
                'refresh-token'
            )
            ->willReturn($refreshToken);
        $mockRefreshTokenPersister = $this->createMock(RefreshTokenRepositoryInterface::class);
        $mockRefreshTokenPersister
            ->expects(self::once())
            ->method('save')
            ->with(self::identicalTo($refreshToken));

        $handle = new AuthenticationUserHandler(
            $mockAuthenticationProvider,
            $stubAccessTokenIssuer,
            $stubRefreshTokenIssuer,
            $mockRefreshTokenInitializer,
            $mockRefreshTokenPersister,
        );

        $authResult = $handle->handle($request);

        self::assertSame(
            'fake-access-token',
            $authResult->accessToken
        );

        self::assertSame(
            'refresh-token',
            $authResult->refreshToken
        );

    }
}

<?php

namespace App\Tests;

use App\Application\Authentication\AuthenticationRefreshTokenHandler;
use App\Application\Authentication\RefreshTokenRequest;
use App\Application\Interfaces\AccessTokenIssuerInterface;
use App\Application\Interfaces\RefreshTokenIssuerInterface;
use App\Application\Interfaces\RefreshTokenVerifierInterface;
use App\Application\RefreshToken\RefreshTokenInitializer;
use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\RefreshToken\RefreshToken;
use App\Domain\User\RefreshToken\RefreshTokenHash;
use App\Domain\User\RefreshToken\RefreshTokenId;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Tests\Fakers\InMemoryRefreshTokenRepository;
use App\Tests\Fakers\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class AuthenticationRefreshTokenHandlerTest extends TestCase
{
    public function testItRefreshTokenHandler(): void
    {
        $email = 'user@example.com';
        $rawRefreshToken = 'refresh_token';
        $accessToken = 'access_token';
        $clockStub = self::createStub(ClockInterface::class);
        $clockStub
            ->method('now')
            ->willReturn(
                new \DateTimeImmutable('2026-10-18 12:00:00')
            );
        $refreshTokenRequest = new RefreshTokenRequest(
            $rawRefreshToken,
        );

        $stubRefreshToken = new RefreshToken(
            new RefreshTokenId('refresh-token-id'),
            new UserId('user-id'),
            new refreshTokenHash('refresh-token-hash'),
            new \DateTimeImmutable('2026-09-17 12:00:00'),
            new \DateTimeImmutable('2026-10-17 12:00:00'),
            null
        );

        $newStubRefreshToken = new RefreshToken(
            new RefreshTokenId('refresh-token-id'),
            new UserId('user-id'),
            new refreshTokenHash('refresh-token-hash'),
            $clockStub->now(),
            new \DateTimeImmutable('2026-10-17 12:00:00'),
            null
        );

        $stubUser = new User(
            new UserId('user-id'),
            new Email($email),
            new Password('hashed-password'),
        );

        $inMemoryRefreshTokenRepository = new InMemoryRefreshTokenRepository($stubRefreshToken);
        $inMemoryUserRepository = new InMemoryUserRepository($stubUser);

        $refreshTokenVerifierMock = self::createMock(RefreshTokenVerifierInterface::class);
        $refreshTokenVerifierMock
            ->expects(self::once())
            ->method('verify')
            ->with($rawRefreshToken)
            ->willReturn($stubRefreshToken);

        $accessTokenIssuerMock = self::createMock(AccessTokenIssuerInterface::class);
        $accessTokenIssuerMock
            ->expects(self::once())
            ->method('issue')
            ->with($stubUser)
            ->willReturn($accessToken);

        $refreshTokenIssuerStub = self::createStub(RefreshTokenIssuerInterface::class);
        $refreshTokenIssuerStub
            ->method('issue')
            ->willReturn($rawRefreshToken);

        $refreshTokenInitializerMock = self::createMock(RefreshTokenInitializer::class);
        $refreshTokenInitializerMock
            ->expects(self::once())
            ->method('initialize')
            ->with($stubUser->getId(), $rawRefreshToken)
            ->willReturn($newStubRefreshToken);

        $handler = new AuthenticationRefreshTokenHandler(
            $refreshTokenVerifierMock,
            $inMemoryUserRepository,
            $accessTokenIssuerMock,
            $refreshTokenIssuerStub,
            $refreshTokenInitializerMock,
            $inMemoryRefreshTokenRepository,
            $clockStub
        );

        $resultAuthenticationResult = $handler->handle($refreshTokenRequest);

        self::assertSame(
            $rawRefreshToken,
            $resultAuthenticationResult->refreshToken,
        );

        self::assertSame(
            $accessToken,
            $resultAuthenticationResult->accessToken,
        );

        self::assertTrue($stubRefreshToken->isRevoked());
    }
}

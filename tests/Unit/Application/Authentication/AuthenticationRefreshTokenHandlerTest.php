<?php

namespace App\Tests\Unit\Application\Authentication;

use App\Application\Authentication\AuthenticationRefreshTokenHandler;
use App\Application\Authentication\RefreshTokenRequest;
use App\Application\Interfaces\AccessTokenIssuerInterface;
use App\Application\Interfaces\RefreshTokenIssuerInterface;
use App\Application\Interfaces\RefreshTokenVerifierInterface;
use App\Application\RefreshToken\RefreshTokenInitializer;
use App\Tests\Fakers\InMemoryRefreshTokenRepository;
use App\Tests\Fakers\InMemoryUserRepository;
use App\Tests\Fixtures\RefreshTokenFixture;
use App\Tests\Fixtures\UserFixture;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class AuthenticationRefreshTokenHandlerTest extends TestCase
{
    public function testItRefreshTokenHandler(): void
    {
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

        $stubRefreshToken = RefreshTokenFixture::create();

        $newStubRefreshToken = RefreshTokenFixture::create(
            createdAt: new \DateTimeImmutable('2026-10-18 12:00:00'),
            expiresAt: new \DateTimeImmutable('2026-11-17 12:00:00'),
        );

        $stubUser = UserFixture::create();

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
            $newStubRefreshToken,
            $inMemoryRefreshTokenRepository->getSavedRefreshToken(),
        );

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

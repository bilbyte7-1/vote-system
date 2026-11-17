<?php

namespace App\Tests\Unit\Infrastructure;

use App\Application\Interfaces\RefreshTokenHasherInterface;
use App\Domain\RefreshToken\RefreshToken;
use App\Infrastructure\Exceptions\RefreshTokenExpiredException;
use App\Infrastructure\Exceptions\RefreshTokenNotFoundException;
use App\Infrastructure\Exceptions\RefreshTokenRevokedException;
use App\Infrastructure\Security\RefreshToken\RefreshTokenVerifier;
use App\Tests\Fakers\InMemoryRefreshTokenRepository;
use App\Tests\Fixtures\RefreshTokenFixture;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class RefreshTokenVerifierTest extends TestCase
{
    private RefreshToken $stubRefreshToken;
    private InMemoryRefreshTokenRepository $inMemoryRepository;
    private RefreshTokenVerifier $verifier;
    private RefreshTokenHasherInterface $refreshTokenHasher;
    private ClockInterface $clock;
    private string $rawRefreshToken = 'refresh-token';
    private string $refreshTokenHash = 'refresh-token-hash';
    private \DateTimeImmutable $now;
    public function setUP(): void
    {
        $this->stubRefreshToken = RefreshTokenFixture::create();
        $this->refreshTokenHasher = self::createMock(RefreshTokenHasherInterface::class);

        $this->clock = self::createStub(ClockInterface::class);
        $this->inMemoryRepository = new InMemoryRefreshTokenRepository($this->stubRefreshToken);

        $this->verifier = new RefreshTokenVerifier(
            $this->refreshTokenHasher,
            $this->inMemoryRepository,
            $this->clock,
        );
    }

    public function testItReturnsValidRefreshToken(): void
    {
        $this->now = new \DateTimeImmutable('2026-09-17 12:00:00 UTC');

        $this->clock
            ->method('now')
            ->willReturn($this->now);

        $this->refreshTokenHasher
            ->expects(self::once())
            ->method('hash')
            ->with($this->rawRefreshToken)
            ->willReturn($this->refreshTokenHash);

        $refreshToken = $this->verifier->verify($this->rawRefreshToken);

        self::assertInstanceOf(RefreshToken::class, $refreshToken);
    }

    public function testItRefreshTokenNotExists(): void
    {
        $this->rawRefreshToken = 'refresh-token-not-exists';
        $this->refreshTokenHash = 'refresh-token-hash-not-exists';

        $this->refreshTokenHasher
            ->expects(self::once())
            ->method('hash')
            ->with($this->rawRefreshToken)
            ->willReturn($this->refreshTokenHash);

        $this->expectException(RefreshTokenNotFoundException::class);
        $this->verifier->verify($this->rawRefreshToken);
    }

    public function testItRefreshTokenExpired(): void
    {
        $this->now = new \DateTimeImmutable('2026-11-17 12:00:00 UTC');
        $this->clock
            ->method('now')
            ->willReturn($this->now);

        $this->refreshTokenHasher
            ->expects(self::once())
            ->method('hash')
            ->with($this->rawRefreshToken)
            ->willReturn($this->refreshTokenHash);

        $this->expectException(RefreshTokenExpiredException::class);
        $this->verifier->verify($this->rawRefreshToken);
    }

    public function testItRejectsRevokedRefreshToken(): void
    {
        $this->stubRefreshToken = RefreshTokenFixture::create(
            revokedAt: new \DateTimeImmutable('2026-09-17 12:05:00 UTC'),
        );

        $this->inMemoryRepository = new InMemoryRefreshTokenRepository(
            $this->stubRefreshToken,
        );

        $this->verifier = new RefreshTokenVerifier(
            $this->refreshTokenHasher,
            $this->inMemoryRepository,
            $this->clock,
        );

        $this->now = new \DateTimeImmutable('2026-09-17 12:10:00 UTC');

        $this->clock
            ->method('now')
            ->willReturn($this->now);

        $this->refreshTokenHasher
            ->expects(self::once())
            ->method('hash')
            ->with($this->rawRefreshToken)
            ->willReturn($this->refreshTokenHash);

        $this->expectException(RefreshTokenRevokedException::class);

        $this->verifier->verify($this->rawRefreshToken);
    }
}

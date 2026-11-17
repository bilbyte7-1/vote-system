<?php

namespace App\Tests\Unit\Domain\RefreshToken;

use App\Domain\RefreshToken\RefreshToken;
use App\Domain\RefreshToken\RefreshTokenHash;
use App\Domain\RefreshToken\RefreshTokenId;
use App\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

class RefreshTokenTest extends TestCase
{
    private RefreshToken $refreshToken;
    private RefreshTokenId $refreshTokenId;
    private RefreshTokenHash $refreshTokenHash;
    private UserId $userId;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $expiresAt;


    public function setUp(): void
    {
        $this->refreshTokenId = new RefreshTokenId('token-id');
        $this->refreshTokenHash = new RefreshTokenHash('hash-token');
        $this->userId = new UserId('user-id');
        $this->createdAt = new \DateTimeImmutable('2026-09-17 12:00:00');
        $this->expiresAt = new \DateTimeImmutable('2026-10-17 13:00:00');

        $this->refreshToken = new RefreshToken(
            $this->refreshTokenId,
            $this->userId,
            $this->refreshTokenHash,
            $this->createdAt,
            $this->expiresAt,
            null
        );
    }

    public function testItRefreshTokenCreate(): void
    {
        self::assertSame($this->refreshTokenId, $this->refreshToken->getId());
        self::assertSame($this->userId, $this->refreshToken->getUserId());
        self::assertSame($this->refreshTokenHash, $this->refreshToken->getTokenHash());
        self::assertSame($this->createdAt, $this->refreshToken->getCreatedAt());
        self::assertSame($this->expiresAt, $this->refreshToken->getExpiresAt());
        self::assertFalse($this->refreshToken->isRevoked());
    }

    public function testItRefreshTokenRevoked(): void
    {
        $this->refreshToken->revoke(new \DateTimeImmutable('2026-10-10 12:00:00'));
        self::assertTrue($this->refreshToken->isRevoked());
    }
}

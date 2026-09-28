<?php

namespace App\Tests;

use App\Application\Interfaces\IdGeneratorInterface;
use App\Application\Interfaces\RefreshTokenHasherInterface;
use App\Application\RefreshToken\RefreshTokenInitializer;
use App\Domain\User\RefreshToken\RefreshToken;
use App\Domain\User\RefreshToken\RefreshTokenHash;
use App\Domain\User\UserId;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\Clock;
use App\Domain\User\RefreshToken\RefreshTokenId;

class RefreshTokenInitializerTest extends TestCase
{
    public function testItInitializeRefreshToken():void
    {
        $refreshToken = new RefreshToken(
            new RefreshTokenId('refresh-token-id'),
            new UserId('user-id'),
            new refreshTokenHash('refresh-token-hash'),
            new \DateTimeImmutable('2026-09-17 12:00:00'),
            new \DateTimeImmutable('2026-10-17 12:00:00'),
            null
        );

        $userId = new UserId('user-id');
        $ttl = 2592000;
        $now = new \DateTimeImmutable('2026-09-17 12:00:00 UTC');

        $stubIdGenerator = $this->createStub(IdGeneratorInterface::class);
        $stubIdGenerator
            ->method('generate')
            ->willReturn('refresh-token-id');

        $stubRefreshTokenHasher = $this->createStub(RefreshTokenHasherInterface::class);
        $stubRefreshTokenHasher
            ->method('hash')
            ->willReturn('refresh-token-hash');
        $stubClock = $this->createStub(ClockInterface::class);
        $stubClock
            ->method('now')
            ->willReturn($now);

        $refreshTokenInitializer = new RefreshTokenInitializer(
            $stubIdGenerator,
            $stubRefreshTokenHasher,
            $stubClock,
            $ttl
        );

        $createdRefreshToken = $refreshTokenInitializer->initialize($userId, 'refresh-token-hash');

        self::assertSame('refresh-token-id', $createdRefreshToken->getId()->getId());
        self::assertSame('user-id', $createdRefreshToken->getUserId()->getId());
        self::assertSame('refresh-token-hash', $createdRefreshToken->getTokenHash()->getHash());

        self::assertEquals(
            new \DateTimeImmutable('2026-09-17 12:00:00 UTC'),
            $createdRefreshToken->getCreatedAt()
        );

        self::assertEquals(
            new \DateTimeImmutable('2026-10-17 12:00:00 UTC'),
            $createdRefreshToken->getExpiresAt()
        );

    }
}

<?php

namespace App\Tests\Unit\Application\RefreshToken;

use App\Application\Interfaces\IdGeneratorInterface;
use App\Application\Interfaces\RefreshTokenHasherInterface;
use App\Application\RefreshToken\RefreshTokenInitializer;
use App\Domain\RefreshToken\RefreshToken;
use App\Domain\RefreshToken\RefreshTokenHash;
use App\Domain\RefreshToken\RefreshTokenId;
use App\Domain\User\UserId;
use App\Tests\Fixtures\RefreshTokenFixture;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class RefreshTokenInitializerTest extends TestCase
{
    public function testItInitializeRefreshToken(): void
    {
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

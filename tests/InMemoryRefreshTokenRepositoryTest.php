<?php

namespace App\Tests;

use App\Domain\User\RefreshToken\RefreshToken;
use App\Domain\User\RefreshToken\RefreshTokenHash;
use App\Domain\User\RefreshToken\RefreshTokenId;
use App\Domain\User\UserId;
use App\Tests\Fakers\InMemoryRefreshTokenRepository;
use PHPUnit\Framework\TestCase;

class InMemoryRefreshTokenRepositoryTest extends TestCase
{
    private RefreshToken $refreshToken;
    private InMemoryRefreshTokenRepository $inMemoryRefreshTokenRepository;
    protected function setUp(): void
    {
        $this->refreshToken = new RefreshToken(
            new RefreshTokenId('refresh-token-id'),
            new UserId('user-id'),
            new refreshTokenHash('refresh-token-hash'),
            new \DateTimeImmutable('2026-09-17 12:00:00'),
            new \DateTimeImmutable('2026-10-17 12:00:00'),
            null
        );

        $this->inMemoryRefreshTokenRepository = new InMemoryRefreshTokenRepository(
            $this->refreshToken
        );
    }

    public function testItSavesRefreshToken(): void
    {
        $this->inMemoryRefreshTokenRepository->save($this->refreshToken);
        self::assertSame(
            $this->refreshToken,
            $this->inMemoryRefreshTokenRepository->getSavedRefreshToken()
        );
    }

    public function testItFindsByHash(): void
    {
        $refreshTokenHash = 'refresh-token-hash';
        $resultRefreshToken = $this->inMemoryRefreshTokenRepository
            ->findByHash($refreshTokenHash);
        self::assertSame(
            $this->refreshToken->getId()->getId(),
            $resultRefreshToken->getId()->getId());
    }

    public function testItNotFoundByhash(): void
    {
        $refreshTokenHash = 'refresh-token-hash-not-exists';
        $resultRefreshToken = $this->inMemoryRefreshTokenRepository->findByHash($refreshTokenHash);
        self::assertNull($resultRefreshToken);;
    }
}

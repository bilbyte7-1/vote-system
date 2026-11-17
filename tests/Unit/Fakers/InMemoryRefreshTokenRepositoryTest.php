<?php

namespace App\Tests\Unit\Fakers;

use App\Domain\RefreshToken\RefreshToken;
use App\Tests\Fakers\InMemoryRefreshTokenRepository;
use App\Tests\Fixtures\RefreshTokenFixture;
use PHPUnit\Framework\TestCase;

class InMemoryRefreshTokenRepositoryTest extends TestCase
{
    private RefreshToken $refreshToken;
    private InMemoryRefreshTokenRepository $inMemoryRefreshTokenRepository;
    protected function setUp(): void
    {
        $this->refreshToken = RefreshTokenFixture::create();

        $this->inMemoryRefreshTokenRepository = new InMemoryRefreshTokenRepository(
            $this->refreshToken
        );
    }

    public function testItSavesRefreshToken(): void
    {
        $repository = new InMemoryRefreshTokenRepository();

        $repository->save($this->refreshToken);

        self::assertSame(
            $this->refreshToken,
            $repository->getSavedRefreshToken(),
        );
    }

    public function testItFindsByHash(): void
    {
        $refreshTokenHash = 'refresh-token-hash';
        $resultRefreshToken = $this->inMemoryRefreshTokenRepository
            ->findByHash($refreshTokenHash);
        self::assertSame(
            $this->refreshToken->getId()->getId(),
            $resultRefreshToken->getId()->getId()
        );
    }

    public function testItNotFoundByhash(): void
    {
        $refreshTokenHash = 'refresh-token-hash-not-exists';
        $resultRefreshToken = $this->inMemoryRefreshTokenRepository->findByHash($refreshTokenHash);
        self::assertNull($resultRefreshToken);
    }

    public function testItRotatesRefreshToken(): void
    {
        $newRefreshToken = RefreshTokenFixture::create(
            id: 'new-refresh-token-id',
            tokenHash: 'new-refresh-token-hash',
        );

        $this->inMemoryRefreshTokenRepository->rotate(
            $this->refreshToken,
            $newRefreshToken,
        );

        self::assertSame(
            $newRefreshToken,
            $this->inMemoryRefreshTokenRepository->getSavedRefreshToken(),
        );
    }
}

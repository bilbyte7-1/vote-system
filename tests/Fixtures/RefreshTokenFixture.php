<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Domain\RefreshToken\RefreshToken;
use App\Domain\RefreshToken\RefreshTokenHash;
use App\Domain\RefreshToken\RefreshTokenId;
use App\Domain\User\UserId;

final class RefreshTokenFixture
{
    public static function create(
        string $id = 'refresh-token-id',
        string $userId = 'user-id',
        string $tokenHash = 'refresh-token-hash',
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $expiresAt = null,
        ?\DateTimeImmutable $revokedAt = null,
    ): RefreshToken {
        return new RefreshToken(
            new RefreshTokenId($id),
            new UserId($userId),
            new RefreshTokenHash($tokenHash),
            $createdAt ?? new \DateTimeImmutable('2026-09-17 12:00:00'),
            $expiresAt ?? new \DateTimeImmutable('2026-10-17 12:00:00'),
            $revokedAt,
        );
    }
}

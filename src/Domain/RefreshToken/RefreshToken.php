<?php

namespace App\Domain\RefreshToken;

use App\Domain\User\UserId;

class RefreshToken
{
    public function __construct(
        private RefreshTokenId $id,
        private UserId $userId,
        private RefreshTokenHash $tokenHash,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $expiresAt,
        private ?\DateTimeImmutable $revokedAt
    ) {
    }

    public function getId(): RefreshTokenId
    {
        return $this->id;
    }
    public function getUserId(): UserId
    {
        return $this->userId;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getTokenHash(): RefreshTokenHash
    {
        return $this->tokenHash;
    }

    public function isExpired(\DateTimeImmutable $now): bool
    {
        return $this->expiresAt <= $now;
    }
    public function isRevoked(): bool
    {
        return $this->revokedAt !== null;
    }
    public function revoke(\DateTimeImmutable $now): void
    {
        $this->revokedAt = $now;
    }
}

<?php

namespace App\Application\RefreshToken;

use App\Application\Interfaces\IdGeneratorInterface;
use App\Application\Interfaces\RefreshTokenHasherInterface;
use App\Domain\RefreshToken\RefreshToken;
use App\Domain\User\UserId;
use App\Domain\RefreshToken\RefreshTokenId;
use App\Domain\RefreshToken\RefreshTokenHash;
use Psr\Clock\ClockInterface;

class RefreshTokenInitializer
{
    public function __construct(
        private IdGeneratorInterface        $idGenerator,
        private RefreshTokenHasherInterface $refreshTokenHasher,
        private ClockInterface $clock,
        private int $ttl
    ){
    }

    public function initialize(
        UserId $userId,
        string $refreshToken
    ){
        $createdAt = $this->clock->now();
        $hash = $this->refreshTokenHasher->hash($refreshToken);

        return new RefreshToken(
            new RefreshTokenId($this->idGenerator->generate()),
            $userId,
            new RefreshTokenHash($hash),
            $createdAt,
            $createdAt->modify(sprintf('+%d seconds', $this->ttl)),
            null
        );

    }
}

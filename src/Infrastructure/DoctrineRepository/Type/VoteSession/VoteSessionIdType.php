<?php

namespace App\Infrastructure\DoctrineRepository\Type\VoteSession;

use App\Domain\VoteSession\VoteSessionId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class VoteSessionIdType extends StringType
{
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?VoteSessionId {
        if ($value === null || $value instanceof VoteSessionId) {
            return $value;
        }

        return new VoteSessionId((string) $value);
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?string {
        return $value?->getId();
    }
}

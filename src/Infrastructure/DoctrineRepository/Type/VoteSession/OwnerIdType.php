<?php

namespace App\Infrastructure\DoctrineRepository\Type\VoteSession;

use App\Domain\VoteSession\OwnerId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class OwnerIdType extends StringType
{
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?OwnerId {
        if ($value === null || $value instanceof OwnerId) {
            return $value;
        }

        return new OwnerId((string) $value);
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?string {
        return $value?->getId();
    }
}

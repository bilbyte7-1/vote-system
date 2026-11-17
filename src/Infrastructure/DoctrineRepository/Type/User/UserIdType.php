<?php

namespace App\Infrastructure\DoctrineRepository\Type\User;

use App\Domain\User\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class UserIdType extends StringType
{
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?UserId {
        if ($value === null || $value instanceof UserId) {
            return $value;
        }
        return new UserId((string) $value);
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?string {
        return $value?->getId();
    }
}

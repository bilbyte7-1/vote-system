<?php

namespace App\Infrastructure\DoctrineRepository\Type\RefreshToken;

use App\Domain\RefreshToken\RefreshTokenId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class RefreshTokenIdType extends StringType
{
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?RefreshTokenId {
        if ($value === null) {
            return null;
        }

        if ($value instanceof RefreshTokenId) {
            return $value;
        }

        return new RefreshTokenId((string) $value);
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?string {
        if ($value === null) {
            return null;
        }

        return $value->getId();
    }
}

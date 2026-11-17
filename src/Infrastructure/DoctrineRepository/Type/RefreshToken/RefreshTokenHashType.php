<?php

namespace App\Infrastructure\DoctrineRepository\Type\RefreshToken;

use App\Domain\RefreshToken\RefreshTokenHash;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class RefreshTokenHashType extends StringType
{
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?RefreshTokenHash {
        if ($value === null || $value instanceof RefreshTokenHash) {
            return $value;
        }

        return new RefreshTokenHash((string) $value);
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?string {
        return $value?->getHash();
    }
}

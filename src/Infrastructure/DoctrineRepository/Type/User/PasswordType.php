<?php

namespace App\Infrastructure\DoctrineRepository\Type\User;

use App\Domain\User\Password;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class PasswordType extends StringType
{
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?Password {
        if ($value === null || $value instanceof Password) {
            return $value;
        }

        return new Password((string) $value);
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?string {
        return $value?->getPasswordHash();
    }
}

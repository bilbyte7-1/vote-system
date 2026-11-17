<?php

namespace App\Infrastructure\DoctrineRepository\Type\User;

use App\Domain\User\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class EmailType extends StringType
{
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?Email {

        if ($value === null || $value instanceof Email) {
            return $value;
        }

        return new Email((string) $value);
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?string {
        return $value?->getEmail();
    }
}

<?php

namespace App\Infrastructure\Id;

use App\Application\Interfaces\IdGeneratorInterface;
use Ramsey\Uuid\Uuid;

final readonly class UuidGenerator implements IdGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid7()->toString();
    }
}

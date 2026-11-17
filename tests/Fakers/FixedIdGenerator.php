<?php

namespace App\Tests\Fakers;

use App\Application\Interfaces\IdGeneratorInterface;

class FixedIdGenerator implements IdGeneratorInterface
{
    public function __construct(
        private string $id,
    ) {
    }

    public function generate(): string
    {
        return $this->id;
    }
}

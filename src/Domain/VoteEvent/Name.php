<?php

namespace App\Domain\VoteEvent;

class Name
{
    public function __construct(
        private string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}

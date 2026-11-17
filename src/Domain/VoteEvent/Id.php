<?php

namespace App\Domain\VoteEvent;

class Id
{
    public function __construct(
        private int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}

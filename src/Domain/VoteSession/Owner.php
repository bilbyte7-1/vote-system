<?php

namespace App\Domain\VoteSession;

class Owner
{
    public function __construct(
        private int $id
    )
    {}

    public function getId(): int
    {
        return $this->id;
    }
}

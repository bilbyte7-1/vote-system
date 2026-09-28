<?php

namespace App\Domain\VoteSession;

class Status
{
    public function __construct(
        private string $status,
    )
    {}

    public function getStatus(): string
    {
        return $this->status;
    }
}

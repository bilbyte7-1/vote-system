<?php

namespace App\Domain\VoteSession;

use InvalidArgumentException;

class VoteSessionId
{
    public function __construct(
        private string $voteSessionId
    ) {
        if ($voteSessionId === '') {
            throw new InvalidArgumentException('VoteSessionId cannot be empty.');
        }
    }

    public function getId(): string
    {
        return $this->voteSessionId;
    }

    public function __toString(): string
    {
        return $this->voteSessionId;
    }
}

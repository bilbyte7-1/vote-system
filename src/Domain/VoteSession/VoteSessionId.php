<?php

namespace App\Domain\VoteSession;

use InvalidArgumentException;

class VoteSessionId
{
    public function __construct(
        private string $voteSessionId
    )
    {
        if ($voteSessionId === '') {
            throw new InvalidArgumentException('UserId cannot be empty.');
        }
    }

    public function getId(): string
    {
        return $this->voteSessionId;
    }
}

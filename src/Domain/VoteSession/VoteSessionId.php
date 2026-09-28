<?php

namespace App\Domain\VoteSession;

class VoteSessionId
{
    public function __construct(
        private int $voteSessionId
    )
    {}

    public function getVoteSessionId(): int
    {
        return $this->voteSessionId;
    }
}

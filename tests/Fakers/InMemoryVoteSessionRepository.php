<?php

namespace App\Tests\Fakers;

use App\Application\Interfaces\VoteSessionRepositoryInterface;
use App\Domain\VoteSession\VoteSession;

class InMemoryVoteSessionRepository implements VoteSessionRepositoryInterface
{
    public function __construct(
        private ?VoteSession $savedVoteSession
    )
    {}

    public function existsActiveVoteSession(): bool
    {
        return $this->savedVoteSession !== null
            && $this->savedVoteSession->getEndedAt() === null;
    }

    public function save(VoteSession $voteSession)
    {
        $this->savedVoteSession = $voteSession;
    }

    public function getSavedVoteSession(): ?VoteSession
    {
        return $this->savedVoteSession;
    }
}

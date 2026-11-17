<?php

namespace App\Application\Interfaces;

use App\Domain\VoteSession\VoteSession;

interface VoteSessionRepositoryInterface
{
    public function existsActiveVoteSession(): bool;

    public function save(VoteSession $voteSession): void;
}

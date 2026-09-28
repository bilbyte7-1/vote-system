<?php

namespace App\Application\VoteSession;

use App\Application\Interfaces\IdGeneratorInterface;
use App\Domain\VoteEvent\VoteEvent;
use App\Domain\VoteSession\Owner;
use App\Domain\VoteSession\Phase;
use App\Domain\VoteSession\Status;
use App\Domain\VoteSession\VoteSession;
use App\Domain\VoteSession\VoteSessionId;

class VoteSessionInitializer
{
    public function __construct(
        VoteSessionId $voteSessionId,
        Status $status,
        Phase $phase,
        IdGeneratorInterface $idGenerator
    ){}

    public function initialize(Owner $owner, VoteEvent $voteEvent): VoteSession
    {
        return new VoteSession(
            new VoteSessionId($this->idGenerator->generate()),
        );
    }
}

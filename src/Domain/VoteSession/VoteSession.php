<?php

namespace App\Domain\VoteSession;

use App\Domain\VoteEvent\VoteEvent;

class VoteSession
{
    public function __construct(
        private VoteSessionId $voteSessionId,
        private Owner $owner,
        private Status $status,
        private array $events,
        private ?Phase $phase = null
    )
    {}

    public function addEvent(VoteEvent $voteEvent): void
    {
        $this->events[] = $voteEvent;
    }
}

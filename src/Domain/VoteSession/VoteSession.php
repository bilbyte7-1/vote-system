<?php

namespace App\Domain\VoteSession;

use App\Domain\VoteEvent\VoteEvent;

class VoteSession
{
    public function __construct(
        private VoteSessionId $voteSessionId,
        private OwnerId $owner,
        private Phase $phase,
        private \DateTimeImmutable $firstPhaseEndsAt,
        private \DateTimeImmutable $secondPhaseEndsAt,
        private ?\DateTimeImmutable $endedAt,
        private array $events = [],
    ) {
    }

    public function addEvent(VoteEvent $voteEvent): void
    {
        $this->events[] = $voteEvent;
    }

    public function end(
        \DateTimeImmutable $at,
    ): void {
        $this->endedAt = $at;
    }

    public function getId(): VoteSessionId
    {
        return $this->voteSessionId;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }
}

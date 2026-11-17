<?php

namespace App\Application\VoteSession;

use App\Application\Interfaces\IdGeneratorInterface;
use App\Domain\VoteSession\OwnerId;
use App\Domain\VoteSession\Phase;
use App\Domain\VoteSession\VoteSession;
use App\Domain\VoteSession\VoteSessionId;
use Symfony\Component\Clock\ClockInterface;

class VoteSessionInitializer
{
    public function __construct(
        private IdGeneratorInterface $idGenerator,
        private ClockInterface $clock,
    ) {
    }

    public function initialize(
        string $ownerId,
        array $voteEvents,
               $firstPhaseDuration,
               $secondPhaseDuration
    ): VoteSession {
        $now = $this->clock->now();
        $firstPhaseEndsAt = $now->modify("+{$firstPhaseDuration} minutes");
        $secondPhaseEndsAt = $firstPhaseEndsAt->modify("+{$secondPhaseDuration} minutes");

        return new VoteSession(
            new VoteSessionId($this->idGenerator->generate()),
            new OwnerId($ownerId),
            Phase::VOTING,
            $firstPhaseEndsAt,
            $secondPhaseEndsAt,
            null,
            $voteEvents,
        );
    }
}

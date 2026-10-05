<?php

namespace App\Application\VoteSession;

use App\Application\Interfaces\IdGeneratorInterface;
use App\Domain\VoteEvent\Id;
use App\Domain\VoteEvent\Name;
use App\Domain\VoteEvent\VoteEvent;
use App\Domain\VoteSession\VoteSession;
use Psr\Clock\ClockInterface;

class VoteEventInitializer
{
    public function __construct(
        private IdGeneratorInterface $idGenerator,
        private ClockInterface $clock,
    )
    {}

    public function initialize(string $evenName): VoteEvent
    {
        $createdAt = $this->clock->now();
        return new VoteEvent(
            new Id($this->idGenerator->generate()),
            new Name($evenName),
            $createdAt
        );
    }
}

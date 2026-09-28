<?php

namespace App\Domain\VoteEvent;

use Symfony\Component\Security\Core\Authorization\Voter\Vote;

class VoteEvent
{
    public function __construct(
        private Id $id,
        private Name $name,
        private \DateTimeImmutable $scheduledAt,
    )
    {}
}

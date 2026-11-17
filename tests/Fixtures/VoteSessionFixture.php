<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Domain\VoteSession\OwnerId;
use App\Domain\VoteSession\Phase;
use App\Domain\VoteSession\VoteSession;
use App\Domain\VoteSession\VoteSessionId;

final class VoteSessionFixture
{
    public static function create(
        string $id = 'vote-session-id',
        string $ownerId = 'user-id',
        Phase $phase = Phase::VOTING,
        ?\DateTimeImmutable $firstPhaseEndsAt = null,
        ?\DateTimeImmutable $secondPhaseEndsAt = null,
        ?\DateTimeImmutable $endedAt = null,
        array $events = [],
    ): VoteSession {
        return new VoteSession(
            new VoteSessionId($id),
            new OwnerId($ownerId),
            $phase,
            $firstPhaseEndsAt ?? new \DateTimeImmutable('2026-10-05 12:15:00'),
            $secondPhaseEndsAt ?? new \DateTimeImmutable('2026-10-05 12:45:00'),
            $endedAt,
            $events,
        );
    }
}

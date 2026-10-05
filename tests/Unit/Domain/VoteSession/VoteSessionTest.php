<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\VoteSession;

use App\Domain\VoteSession\OwnerId;
use App\Domain\VoteSession\Phase;
use App\Domain\VoteSession\VoteSession;
use App\Domain\VoteSession\VoteSessionId;
use PHPUnit\Framework\TestCase;

final class VoteSessionTest extends TestCase
{
    public function testItReturnsItsId(): void
    {
        $id = new VoteSessionId('vote-session-id');
        $session = $this->createVoteSession(id: $id);

        self::assertSame($id, $session->getId());
    }

    public function testItIsNotEndedInitially(): void
    {
        $session = $this->createVoteSession();

        self::assertNull($session->getEndedAt());
    }

    public function testItCanBeEnded(): void
    {
        $session = $this->createVoteSession();
        $endedAt = new \DateTimeImmutable('2026-10-05 14:00:00');

        $session->end($endedAt);

        self::assertSame($endedAt, $session->getEndedAt());
    }

    private function createVoteSession(?VoteSessionId $id = null): VoteSession
    {
        return new VoteSession(
            $id ?? new VoteSessionId('vote-session-id'),
            new OwnerId('user-id'),
            Phase::VOTING,
            new \DateTimeImmutable('2026-10-05 12:15:00'),
            new \DateTimeImmutable('2026-10-05 12:45:00'),
            null,
            [],
        );
    }
}

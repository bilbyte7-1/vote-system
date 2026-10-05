<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\VoteSession;

use App\Application\Interfaces\IdGeneratorInterface;
use App\Application\VoteSession\VoteSessionInitializer;
use App\Domain\VoteSession\OwnerId;
use App\Domain\VoteSession\Phase;
use App\Domain\VoteSession\VoteSession;
use App\Domain\VoteSession\VoteSessionId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;

final class VoteSessionInitializerTest extends TestCase
{
    public function testItInitializesVotingSession(): void
    {
        $expected = new VoteSession(
            new VoteSessionId('vote-session-id'),
            new OwnerId('user-id'),
            Phase::VOTING,
            new \DateTimeImmutable('2026-10-05 12:15:00 UTC'),
            new \DateTimeImmutable('2026-10-05 12:45:00 UTC'),
            null,
            []
        );

        $idGenerator = $this->createMock(IdGeneratorInterface::class);
        $idGenerator
            ->expects(self::once())
            ->method('generate')
            ->willReturn('vote-session-id');

        $clock = new Clock(new MockClock('2026-10-05 12:00:00 UTC'));
        $initializer = new VoteSessionInitializer($idGenerator, $clock);

        $session = $initializer->initialize(
            'user-id',
            [],
            15,
            30,
        );
        self::assertEquals($expected, $session);
    }
}

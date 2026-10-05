<?php

namespace App\Tests;

use App\Domain\VoteSession\OwnerId;
use App\Domain\VoteSession\Phase;
use App\Domain\VoteSession\VoteSession;
use App\Domain\VoteSession\VoteSessionId;
use App\Tests\Fakers\InMemoryVoteSessionRepository;
use PHPUnit\Framework\TestCase;

class InMemoryVoteSessionRepositoryTest extends TestCase
{
    private VoteSession $voteSession;
    protected function setUp(): void
    {
        $this->voteSession = new VoteSession(
            new VoteSessionId('vote-session-id'),
            new OwnerId('owner-id'),
            Phase::VOTING,
            new \DateTimeImmutable('2026-10-02 12:00:00'),
            new \DateTimeImmutable('2026-10-02 13:00:00'),
            null,
            [],
            );
    }

    public function testItReturnsInitiallyProvidedVoteSession(): void
    {
        $voteSessionRepository = new InMemoryVoteSessionRepository($this->voteSession);
        self::assertSame($this->voteSession, $voteSessionRepository->getSavedVoteSession());
    }

    public function testItExistsActiveVoteSession(): void
    {
        $voteSessionRepository = new InMemoryVoteSessionRepository($this->voteSession);
        self::assertTrue($voteSessionRepository->existsActiveVoteSession());
    }

    public function testItSavesVoteSession(): void
    {
        $voteSessionRepository = new InMemoryVoteSessionRepository(null);
        $voteSessionRepository->save($this->voteSession);

        self::assertSame($this->voteSession, $voteSessionRepository->getSavedVoteSession());
    }
}

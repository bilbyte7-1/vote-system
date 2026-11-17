<?php

namespace App\Tests\Unit\Fakers;

use App\Domain\VoteSession\VoteSession;
use App\Tests\Fakers\InMemoryVoteSessionRepository;
use App\Tests\Fixtures\VoteSessionFixture;
use PHPUnit\Framework\TestCase;

class InMemoryVoteSessionRepositoryTest extends TestCase
{
    private VoteSession $voteSession;
    protected function setUp(): void
    {
        $this->voteSession = VoteSessionFixture::create();
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
        $voteSessionRepository = new InMemoryVoteSessionRepository();
        $voteSessionRepository->save($this->voteSession);

        self::assertSame($this->voteSession, $voteSessionRepository->getSavedVoteSession());
    }
}

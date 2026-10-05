<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\VoteSessionStart;

use App\Application\Exceptions\ActiveVoteSessionAlreadyExists;
use App\Application\Interfaces\VoteSessionRepositoryInterface;
use App\Application\Interfaces\VoteSessionStartLockInterface;
use App\Application\VoteSession\VoteSessionInitializer;
use App\Application\VoteSessionStart\VoteSessionStartHandler;
use App\Application\VoteSessionStart\VoteSessionStartRequest;
use App\Domain\VoteSession\OwnerId;
use App\Domain\VoteSession\Phase;
use App\Domain\VoteSession\VoteSession;
use App\Domain\VoteSession\VoteSessionId;
use PHPUnit\Framework\TestCase;

final class VoteSessionStartHandlerTest extends TestCase
{
    private const int FIRST_PHASE_DURATION = 15;
    private const int SECOND_PHASE_DURATION = 30;
    private const string USER_ID = 'user-id';

    public function testItCreatesAndSavesVoteSession(): void
    {
        $initializer = $this->createMock(VoteSessionInitializer::class);
        $repository = $this->createMock(VoteSessionRepositoryInterface::class);
        $startLock = $this->createMock(VoteSessionStartLockInterface::class);

        $sessionId = new VoteSessionId('vote-session-id');
        $session = $this->createVoteSession($sessionId);

        $startLock
            ->expects(self::once())
            ->method('execute')
            ->willReturnCallback(
                static fn (callable $operation): mixed => $operation()
            );

        $repository
            ->expects(self::once())
            ->method('existsActiveVoteSession')
            ->willReturn(false);

        $initializer
            ->expects(self::once())
            ->method('initialize')
            ->with(self::USER_ID, [], self::FIRST_PHASE_DURATION,self::SECOND_PHASE_DURATION)
            ->willReturn($session);

        $repository
            ->expects(self::once())
            ->method('save')
            ->with($session);

        $handler = new VoteSessionStartHandler(
            $initializer,
            $startLock,
            $repository,
        );

        $result = $handler->handle(
            new VoteSessionStartRequest(
                self::USER_ID,
                self::FIRST_PHASE_DURATION,
                self::SECOND_PHASE_DURATION
            ),
        );

        self::assertSame($sessionId, $result);
    }

    public function testItRejectsStartWhenActiveSessionAlreadyExists(): void
    {
        $initializer = $this->createMock(VoteSessionInitializer::class);
        $repository = $this->createMock(VoteSessionRepositoryInterface::class);
        $startLock = $this->createMock(VoteSessionStartLockInterface::class);

        $startLock
            ->expects(self::once())
            ->method('execute')
            ->willReturnCallback(
                static fn (callable $operation): mixed => $operation()
            );

        $repository
            ->expects(self::once())
            ->method('existsActiveVoteSession')
            ->willReturn(true);

        $initializer
            ->expects(self::never())
            ->method('initialize');

        $repository
            ->expects(self::never())
            ->method('save');

        $handler = new VoteSessionStartHandler(
            $initializer,
            $startLock,
            $repository,
        );

        $this->expectException(ActiveVoteSessionAlreadyExists::class);

        $handler->handle(
            new VoteSessionStartRequest(
                self::USER_ID,
                self::FIRST_PHASE_DURATION,
                self::SECOND_PHASE_DURATION
            ),
        );
    }

    private function createVoteSession(VoteSessionId $id): VoteSession
    {
        return new VoteSession(
            $id,
            new OwnerId(self::USER_ID),
            Phase::VOTING,
            new \DateTimeImmutable('2026-10-05 12:15:00'),
            new \DateTimeImmutable('2026-10-05 12:45:00'),
            null,
            [],
        );
    }
}

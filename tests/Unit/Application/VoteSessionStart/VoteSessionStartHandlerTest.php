<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\VoteSessionStart;

use App\Application\Exceptions\ActiveVoteSessionAlreadyExists;
use App\Application\Interfaces\NotificationPublisherInterface;
use App\Application\Interfaces\VoteSessionRepositoryInterface;
use App\Application\Interfaces\VoteSessionStartLockInterface;
use App\Application\Messages\VoteSessionStarted;
use App\Application\VoteSession\VoteSessionInitializer;
use App\Application\VoteSessionStart\VoteSessionStartHandler;
use App\Application\VoteSessionStart\VoteSessionStartRequest;
use App\Tests\Fixtures\VoteSessionFixture;
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
        $notificationPublisher = $this->createMock(NotificationPublisherInterface::class);

        $session = VoteSessionFixture::create();
        $sessionId = $session->getId();

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
            ->with(
                self::USER_ID,
                [],
                self::FIRST_PHASE_DURATION,
                self::SECOND_PHASE_DURATION,
            )
            ->willReturn($session);

        $repository
            ->expects(self::once())
            ->method('save')
            ->with($session);

        $notificationPublisher
            ->expects(self::once())
            ->method('notify')
            ->with(self::isInstanceOf(VoteSessionStarted::class));

        $handler = new VoteSessionStartHandler(
            $initializer,
            $startLock,
            $repository,
            $notificationPublisher,
        );

        $result = $handler->handle(
            new VoteSessionStartRequest(
                self::USER_ID,
                self::FIRST_PHASE_DURATION,
                self::SECOND_PHASE_DURATION,
            ),
        );

        self::assertSame($sessionId, $result);
    }

    public function testItRejectsStartWhenActiveSessionAlreadyExists(): void
    {
        $initializer = $this->createMock(VoteSessionInitializer::class);
        $repository = $this->createMock(VoteSessionRepositoryInterface::class);
        $startLock = $this->createMock(VoteSessionStartLockInterface::class);
        $notificationPublisher = $this->createMock(NotificationPublisherInterface::class);

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

        $notificationPublisher
            ->expects(self::never())
            ->method('notify');

        $handler = new VoteSessionStartHandler(
            $initializer,
            $startLock,
            $repository,
            $notificationPublisher,
        );

        $this->expectException(ActiveVoteSessionAlreadyExists::class);

        $handler->handle(
            new VoteSessionStartRequest(
                self::USER_ID,
                self::FIRST_PHASE_DURATION,
                self::SECOND_PHASE_DURATION,
            ),
        );
    }
}

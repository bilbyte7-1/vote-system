<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Controller;

use App\Application\VoteSessionStart\VoteSessionStartHandler;
use App\Application\VoteSessionStart\VoteSessionStartRequest;
use App\Controller\VoteSessionStartController;
use App\Domain\VoteSession\VoteSessionId;
use App\Infrastructure\Security\SymfonySecurityUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class VoteSessionStartControllerTest extends TestCase
{
    public function testItMapsHttpRequestAndReturnsCreatedResponse(): void
    {
        $firstPhaseDuration = 15;
        $secondPhaseDuration = 30;
        $userId = 'user-id';

        $user = $this->createMock(SymfonySecurityUser::class);
        $user
            ->expects(self::once())
            ->method('getUserIdentifier')
            ->willReturn($userId);

        $handler = $this->createMock(VoteSessionStartHandler::class);
        $handler
            ->expects(self::once())
            ->method('handle')
            ->with(
                self::callback(
                    static fn (VoteSessionStartRequest $voteSessionStartRequest): bool =>
                        $voteSessionStartRequest->userId === $userId
                        && $voteSessionStartRequest->firstDuration === $firstPhaseDuration
                        && $voteSessionStartRequest->secondDuration === $secondPhaseDuration
                )
            )
            ->willReturn(new VoteSessionId('vote-session-id'));

        $request = Request::create(
            '/vote-sessions',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'first_phase_duration' => $firstPhaseDuration,
                'second_phase_duration' => $secondPhaseDuration,
            ], JSON_THROW_ON_ERROR),
        );

        $controller = new VoteSessionStartController();

        $response = $controller(
            $user,
            $handler,
            $request,
        );

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame(
            '/vote-sessions/vote-session-id',
            $response->headers->get('Location'),
        );
        self::assertSame('', $response->getContent());
    }
}

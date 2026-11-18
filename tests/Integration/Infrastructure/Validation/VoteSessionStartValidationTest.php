<?php

namespace App\Tests\Integration\Infrastructure\Validation;

final class VoteSessionStartValidationTest extends RequestValidationTestCase
{
    public function testVoteSessionStartRequestIsValid(): void
    {
        self::expectNotToPerformAssertions();

        $event = $this->createRequestEvent(
            [
                'first_phase_duration' => 30,
                'second_phase_duration' => 15,
            ],
            '/vote-sessions',
            'vote_sessions_start',
        );

        $this->requestListener->onKernelRequest($event);
    }

    public function testFirstPhaseDurationIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [
                'first_phase_duration' => '',
                'second_phase_duration' => 15,
            ],
            '/vote-sessions',
            'vote_sessions_start',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['FIRST_PHASE_DURATION_REQUIRED'],
            $responseContent['errors']['[first_phase_duration]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testSecondPhaseDurationIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [
                'first_phase_duration' => 30,
                'second_phase_duration' => '',
            ],
            '/vote-sessions',
            'vote_sessions_start',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['SECOND_PHASE_DURATION_REQUIRED'],
            $responseContent['errors']['[second_phase_duration]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testFirstPhaseDurationMustBeInteger(): void
    {
        $event = $this->createRequestEvent(
            [
                'first_phase_duration' => 'thirty',
                'second_phase_duration' => 15,
            ],
            '/vote-sessions',
            'vote_sessions_start',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['FIRST_PHASE_DURATION_MUST_BE_INTEGER'],
            $responseContent['errors']['[first_phase_duration]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testSecondPhaseDurationMustBeInteger(): void
    {
        $event = $this->createRequestEvent(
            [
                'first_phase_duration' => 30,
                'second_phase_duration' => 'fifteen',
            ],
            '/vote-sessions',
            'vote_sessions_start',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['SECOND_PHASE_DURATION_MUST_BE_INTEGER'],
            $responseContent['errors']['[second_phase_duration]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testFirstPhaseDurationMustBePositive(): void
    {
        $event = $this->createRequestEvent(
            [
                'first_phase_duration' => 0,
                'second_phase_duration' => 15,
            ],
            '/vote-sessions',
            'vote_sessions_start',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['FIRST_PHASE_DURATION_MUST_BE_POSITIVE'],
            $responseContent['errors']['[first_phase_duration]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testSecondPhaseDurationMustBePositive(): void
    {
        $event = $this->createRequestEvent(
            [
                'first_phase_duration' => 30,
                'second_phase_duration' => -1,
            ],
            '/vote-sessions',
            'vote_sessions_start',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['SECOND_PHASE_DURATION_MUST_BE_POSITIVE'],
            $responseContent['errors']['[second_phase_duration]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testFirstPhaseDurationFieldIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [
                'second_phase_duration' => 15,
            ],
            '/vote-sessions',
            'vote_sessions_start',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertArrayHasKey(
            '[first_phase_duration]',
            $responseContent['errors']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testSecondPhaseDurationFieldIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [
                'first_phase_duration' => 30,
            ],
            '/vote-sessions',
            'vote_sessions_start',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertArrayHasKey(
            '[second_phase_duration]',
            $responseContent['errors']
        );

        self::assertSame(422, $response->getStatusCode());
    }
}

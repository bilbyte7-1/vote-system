<?php

namespace App\Tests\Integration\Infrastructure\Validation;

final class RefreshTokenValidationTest extends RequestValidationTestCase
{
    public function testRefreshTokenRequestIsValid(): void
    {
        self::expectNotToPerformAssertions();

        $event = $this->createRequestEvent(
            [
                'refresh_token' => 'some-valid-refresh-token',
            ],
            '/users/refresh',
            'users_refresh',
        );

        $this->requestListener->onKernelRequest($event);
    }

    public function testRefreshTokenIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [
                'refresh_token' => '',
            ],
            '/users/refresh',
            'users_refresh',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['REFRESH_TOKEN_REQUIRED'],
            $responseContent['errors']['[refresh_token]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testRefreshTokenFieldIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [],
            '/users/refresh',
            'users_refresh',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertArrayHasKey(
            '[refresh_token]',
            $responseContent['errors']
        );

        self::assertSame(422, $response->getStatusCode());
    }
}

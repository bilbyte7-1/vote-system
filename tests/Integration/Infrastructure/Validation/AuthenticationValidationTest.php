<?php

namespace App\Tests\Integration\Infrastructure\Validation;

final class AuthenticationValidationTest extends RequestValidationTestCase
{
    public function testLoginRequestIsValid(): void
    {
        self::expectNotToPerformAssertions();

        $event = $this->createRequestEvent(
            [
                'email' => 'user@example.com',
                'password' => 'password123',
            ],
            '/users/login',
            'users_login',
        );

        $this->requestListener->onKernelRequest($event);
    }

    public function testLoginEmailIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [
                'email' => '',
                'password' => 'password123',
            ],
            '/users/login',
            'users_login',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['EMAIL_REQUIRED'],
            $responseContent['errors']['[email]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testLoginEmailIsInvalid(): void
    {
        $event = $this->createRequestEvent(
            [
                'email' => 'invalid-email',
                'password' => 'password123',
            ],
            '/users/login',
            'users_login',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertArrayHasKey(
            '[email]',
            $responseContent['errors']
        );

        self::assertCount(
            1,
            $responseContent['errors']['[email]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testLoginPasswordIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [
                'email' => 'user@example.com',
                'password' => '',
            ],
            '/users/login',
            'users_login',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['PASSWORD_REQUIRED'],
            $responseContent['errors']['[password]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testEmptyLoginEmailError(): void
    {
        $event = $this->createRequestEvent(
            [
                'email' => '',
                'password' => 'password123',
            ],
            '/users/login',
            'users_login',
        );

        [, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['EMAIL_REQUIRED'],
            $responseContent['errors']['[email]']
        );

        self::assertCount(
            1,
            $responseContent['errors']['[email]']
        );
    }

    public function testLoginPasswordFieldIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [
                'email' => 'user@example.com',
            ],
            '/users/login',
            'users_login',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertArrayHasKey(
            '[password]',
            $responseContent['errors']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testLoginEmailFieldIsRequired(): void
    {
        $event = $this->createRequestEvent(
            [
                'password' => 'password123',
            ],
            '/users/login',
            'users_login',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertArrayHasKey(
            '[email]',
            $responseContent['errors']
        );

        self::assertSame(422, $response->getStatusCode());
    }
}

<?php

namespace App\Tests\Integration\Infrastructure\Validation;

final class RegistrationValidationTest extends RequestValidationTestCase
{
    public function testValidRequestInputIsAccepted(): void
    {
        self::expectNotToPerformAssertions();

        $event = $this->createRequestEvent(
            [
                'email' => '1@example.com',
                'plainPassword' => 'password123',
                'confirmPassword' => 'password123',
            ],
            '/users/register',
            'users_register',
        );

        $this->requestListener->onKernelRequest($event);
    }

    public function testInvalidRequestInputIsRejected(): void
    {
        $event = $this->createRequestEvent(
            [
                'email' => '',
                'plainPassword' => 'password123',
                'confirmPassword' => 'password123',
            ],
            '/users/register',
            'users_register',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['EMAIL_REQUIRED'],
            $responseContent['errors']['[email]']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    public function testRequestWithoutValidationStrategy(): void
    {
        self::expectNotToPerformAssertions();

        $event = $this->createRequestEvent(
            [
                'email' => '',
                'plainPassword' => '',
                'confirmPassword' => '',
            ],
            '/users/register',
            'some_route_without_validation',
        );

        $this->requestListener->onKernelRequest($event);
    }

    public function testConfirmPasswordNotMatching(): void
    {
        $event = $this->createRequestEvent(
            [
                'email' => '1@example.com',
                'plainPassword' => 'password123',
                'confirmPassword' => 'password',
            ],
            '/users/register',
            'users_register',
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['PASSWORDS_DO_NOT_MATCH'],
            $responseContent['errors']['[confirmPassword]']
        );

        self::assertSame(422, $response->getStatusCode());
    }
}

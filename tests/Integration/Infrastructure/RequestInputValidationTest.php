<?php

namespace App\Tests\Integration\Infrastructure;

use App\Infrastructure\Validation\ConcreteValidationStrategySelector;
use App\Infrastructure\Validation\ErrorResponseListener;
use App\Infrastructure\Validation\Exceptions\RequestValidationException;
use App\Infrastructure\Validation\RequestValidationDispatcher;
use App\Infrastructure\Validation\RequestValidationListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestInputValidationTest extends KernelTestCase
{
    private RequestValidationListener $requestListener;
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $selector = static::getContainer()->get(
            ConcreteValidationStrategySelector::class
        );

        $this->requestListener = new RequestValidationListener(
            new RequestValidationDispatcher(),
            $selector,
        );
    }

    public function testValidRequestInputIsAccepted(): void
    {
        self::expectNotToPerformAssertions();

        $event = $this->createRequestEvent([
            'email' => '1@example.com',
            'plainPassword' => 'password123',
            'confirmPassword' => 'password123',
        ]);

        $this->requestListener->onKernelRequest($event);
    }

    public function testInvalidRequestInputIsRejected(): void
    {
        $event = $this->createRequestEvent([
            'email' => '',
            'plainPassword' => 'password123',
            'confirmPassword' => 'password123',
        ]);
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
            'some_route_without_validation',
        );

        $this->requestListener->onKernelRequest($event);
    }

    public function testConfirmPasswordNotMatching(): void
    {
        $event = $this->createRequestEvent([
            'email' => '1@example.com',
            'plainPassword' => 'password123',
            'confirmPassword' => 'password',
        ]);
        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertSame(
            ['PASSWORDS_DO_NOT_MATCH'],
            $responseContent['errors']['[confirmPassword]']
        );

        self::assertSame(422, $response->getStatusCode());
    }
    private function createRequestEvent(
        array $data,
        string $route = 'users_register'
    ): RequestEvent {
        $request = Request::create(
            '/users/register',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data),
        );

        $request->attributes->set('_route', $route);

        return new RequestEvent(
            self::$kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
    }

    public function testLoginRequestIsValid(): void
    {
        self::expectNotToPerformAssertions();

        $event = $this->createRequestEvent(
            [
                'email' => 'user@example.com',
                'password' => 'password123',
            ],
            'users_login'
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
            'users_login'
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
            'users_login'
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
            'users_login'
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
            'users_login'
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
            'users_login'
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
            'users_login'
        );

        [$response, $responseContent] =
            $this->getValidationErrorResponse($event);

        self::assertArrayHasKey(
            '[email]',
            $responseContent['errors']
        );

        self::assertSame(422, $response->getStatusCode());
    }

    private function getValidationErrorResponse(
        RequestEvent $event,
    ): array {
        $request = $event->getRequest();

        try {
            $this->requestListener->onKernelRequest($event);

            self::fail('RequestValidationException was not thrown.');
        } catch (RequestValidationException $exception) {
            $exceptionEvent = new ExceptionEvent(
                self::$kernel,
                $request,
                HttpKernelInterface::MAIN_REQUEST,
                $exception,
            );
        }

        $responseListener = self::getContainer()->get(
            ErrorResponseListener::class
        );

        $responseListener->onKernelException($exceptionEvent);

        $response = $exceptionEvent->getResponse();

        return [
            $response,
            json_decode($response->getContent(), true),
        ];
    }
}

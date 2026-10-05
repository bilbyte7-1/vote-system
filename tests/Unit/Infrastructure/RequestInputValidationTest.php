<?php

namespace App\Tests\Unit\Infrastructure;

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

        $event = self::createStub(RequestEvent::class);
        $event
            ->method('getRequest')
            ->willReturn($request);

        return $event;
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

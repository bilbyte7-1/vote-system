<?php

namespace App\Tests\Integration\Infrastructure\Validation;

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

abstract class RequestValidationTestCase extends KernelTestCase
{
    protected RequestValidationListener $requestListener;

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

    protected function createRequestEvent(
        array $data,
        string $path,
        string $route,
    ): RequestEvent {
        $request = Request::create(
            $path,
            'POST',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode($data),
        );

        $request->attributes->set('_route', $route);

        return new RequestEvent(
            self::$kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
    }

    protected function getValidationErrorResponse(
        RequestEvent $event,
    ): array {
        $request = $event->getRequest();

        try {
            $this->requestListener->onKernelRequest($event);

            self::fail(
                'RequestValidationException was not thrown.'
            );
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
            json_decode(
                $response->getContent(),
                true,
            ),
        ];
    }
}

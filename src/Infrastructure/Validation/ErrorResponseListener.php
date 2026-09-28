<?php

namespace App\Infrastructure\Validation;

use App\Infrastructure\Validation\Exceptions\RequestValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class ErrorResponseListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof RequestValidationException) {
            return;
        }

        $errors = [];

        foreach ($exception->violations as $violation) {
            $errors[$violation->getPropertyPath()][] =
                $violation->getMessage();
        }

        $event->setResponse(
            new JsonResponse(
                [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Request validation failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            )
        );
    }
}

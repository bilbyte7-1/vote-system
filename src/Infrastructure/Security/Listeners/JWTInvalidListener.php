<?php

namespace App\Infrastructure\Security\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsEventListener]
final class JWTInvalidListener
{
    public function __invoke(JWTInvalidEvent $event): void
    {
        $event->setResponse(
            new JsonResponse(
                ['code' => 'invalid_token'],
                JsonResponse::HTTP_UNAUTHORIZED,
            )
        );
    }
}

<?php

namespace App\Infrastructure\Security\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsEventListener]
final class JWTExpiredListener
{
    public function __invoke(JWTExpiredEvent $event): void
    {
        $event->setResponse(
            new JsonResponse(
                ['code' => 'token_expired'],
                JsonResponse::HTTP_UNAUTHORIZED,
            )
        );
    }
}

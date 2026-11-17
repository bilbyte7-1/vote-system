<?php

namespace App\Infrastructure\Validation;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestValidationListener
{
    public function __construct(
        private RequestValidationDispatcher $dispatcher,
        private ConcreteValidationStrategySelector $strategySelector
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        $concreteStrategy = $this->strategySelector->select($route);
        if (null === $concreteStrategy) {
            return;
        }
        $this->dispatcher->dispatch($concreteStrategy, $request);
    }
}

<?php

namespace App\Infrastructure\Validation;

use App\Infrastructure\Interfaces\RequestValidationStrategyInterface;
use Psr\Container\ContainerInterface;

final readonly class ConcreteValidationStrategySelector
{
    public function __construct(
        private ContainerInterface $strategies,
    ) {
    }

    public function select(string $route): ?RequestValidationStrategyInterface
    {
        if (!$this->strategies->has($route)) {
            return null;
        }

        $strategy = $this->strategies->get($route);

        if (!$strategy instanceof RequestValidationStrategyInterface) {
            throw new \LogicException('Invalid validation strategy.');
        }

        return $strategy;
    }
}

<?php

namespace App\Infrastructure\Validation;

use App\Infrastructure\Interfaces\RequestValidationStrategyInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestValidationDispatcher
{
    public function dispatch(
        RequestValidationStrategyInterface $strategy,
        Request $request,
    ): void {
        $strategy->validate($request);
    }
}

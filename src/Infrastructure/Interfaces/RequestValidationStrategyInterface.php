<?php

namespace App\Infrastructure\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface RequestValidationStrategyInterface
{
    public function validate(Request $request): void;
}

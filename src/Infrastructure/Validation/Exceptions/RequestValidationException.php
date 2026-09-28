<?php

namespace App\Infrastructure\Validation\Exceptions;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidationException extends \RuntimeException
{
    public function __construct(
        public readonly ConstraintViolationListInterface $violations,
    ) {
        parent::__construct('Request validation failed.');
    }
}

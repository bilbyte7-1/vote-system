<?php

namespace App\Infrastructure\Validation\Authentication;

use App\Infrastructure\Interfaces\RequestValidationStrategyInterface;
use App\Infrastructure\Validation\Exceptions\RequestValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class AuthenticationValidationStrategy implements RequestValidationStrategyInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function validate(Request $request): void
    {
        $data = $request->toArray();

        $violations = $this->validator->validate(
            $data,
            new Assert\Collection([
                'email' => new Assert\Sequentially([
                    new Assert\NotBlank(
                        message: 'EMAIL_REQUIRED',
                    ),
                    new Assert\Email(),
                ]),
                'password' => [
                    new Assert\NotBlank(
                        message: 'PASSWORD_REQUIRED',
                    ),
                ],
            ]),
        );

        if (count($violations) > 0) {
            throw new RequestValidationException($violations);
        }
    }
}


<?php

namespace App\Infrastructure\Validation\Register;

use App\Infrastructure\Interfaces\RequestValidationStrategyInterface;
use App\Infrastructure\Validation\Exceptions\RequestValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterValidationStrategy implements RequestValidationStrategyInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator
    )
    {}

    public function validate(Request $request): void
    {
        $data = $request->toArray();

        $violations = $this->validator->validate(
            $data,
            new Assert\Sequentially([
                new Assert\Collection([
                    'email' => [
                        new Assert\NotBlank(
                            message: 'EMAIL_REQUIRED',
                        ),
                        new Assert\Email(),
                    ],
                    'plainPassword' => [
                        new Assert\NotBlank(),
                        new Assert\Length(min: 8, max: 255),
                    ],
                    'confirmPassword' => [],
                ]),
                new Assert\Callback(
                    function (
                        array $data,
                        ExecutionContextInterface $context,
                    ): void {
                        if ($data['plainPassword'] !== $data['confirmPassword']) {
                            $context
                                ->buildViolation('PASSWORDS_DO_NOT_MATCH')
                                ->atPath('[confirmPassword]')
                                ->addViolation();
                        }
                    }
                ),
            ]),
        );

        if (count($violations) > 0) {
            throw new RequestValidationException($violations);
        }
    }
}

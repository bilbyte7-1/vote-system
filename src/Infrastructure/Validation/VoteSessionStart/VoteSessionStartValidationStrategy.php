<?php

namespace App\Infrastructure\Validation\VoteSessionStart;

use App\Infrastructure\Interfaces\RequestValidationStrategyInterface;
use App\Infrastructure\Validation\Exceptions\RequestValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class VoteSessionStartValidationStrategy implements RequestValidationStrategyInterface
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
                'first_phase_duration' => new Assert\Sequentially([
                    new Assert\NotBlank(
                        message: 'FIRST_PHASE_DURATION_REQUIRED',
                    ),
                    new Assert\Type(
                        type: 'integer',
                        message: 'FIRST_PHASE_DURATION_MUST_BE_INTEGER',
                    ),
                    new Assert\Positive(
                        message: 'FIRST_PHASE_DURATION_MUST_BE_POSITIVE',
                    ),
                ]),
                'second_phase_duration' => new Assert\Sequentially([
                    new Assert\NotBlank(
                        message: 'SECOND_PHASE_DURATION_REQUIRED',
                    ),
                    new Assert\Type(
                        type: 'integer',
                        message: 'SECOND_PHASE_DURATION_MUST_BE_INTEGER',
                    ),
                    new Assert\Positive(
                        message: 'SECOND_PHASE_DURATION_MUST_BE_POSITIVE',
                    ),
                ]),
            ]),
        );

        if (count($violations) > 0) {
            throw new RequestValidationException($violations);
        }
    }
}

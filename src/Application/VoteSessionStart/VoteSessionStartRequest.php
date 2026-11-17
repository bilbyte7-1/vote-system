<?php

namespace App\Application\VoteSessionStart;

final readonly class VoteSessionStartRequest
{
    public function __construct(
        public string $userId,
        public int $firstDuration,
        public int $secondDuration
    ) {
    }
}

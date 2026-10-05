<?php

namespace App\Application\VoteSessionStart;

class VoteSessionStartRequest
{
    public function __construct(
        public string $userId,
        public int $firstDuration,
        public int $secondDuration
    )
    {}
}

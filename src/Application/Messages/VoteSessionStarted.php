<?php

namespace App\Application\Messages;

use App\Application\Interfaces\ApplicationMessageInterface;
use App\Domain\VoteSession\VoteSessionId;

final readonly class VoteSessionStarted implements ApplicationMessageInterface
{
    public const TYPE = 'vote_session_started';

    public function __construct(
        public VoteSessionId $voteSessionId,
    ) {
    }
}

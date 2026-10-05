<?php

namespace App\Domain\VoteSession;

enum EndReason: string
{
    case COMPLETED = 'completed';
    case RESET = 'reset';
    case EMPTY_TIMEOUT = 'empty_timeout';
}

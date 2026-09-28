<?php

namespace App\Domain\VoteSession;

enum Phase: string
{
    case VOTING = 'voting';
    case CONSENSUS = 'consensus';
}

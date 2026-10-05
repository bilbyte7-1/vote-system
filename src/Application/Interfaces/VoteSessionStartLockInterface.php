<?php

namespace App\Application\Interfaces;

interface VoteSessionStartLockInterface
{
    public function execute(callable $operation): mixed;
}

<?php

namespace App\Infrastructure\Lock;

use App\Application\Interfaces\VoteSessionStartLockInterface;
use Symfony\Component\Lock\LockFactory;

final readonly class SymfonyVoteSessionStartLock
    implements VoteSessionStartLockInterface
{
    public function __construct(
        private LockFactory $lockFactory,
    ) {
    }

    public function execute(callable $operation): mixed
    {
        $lock = $this->lockFactory->createLock(
            'vote-session-start'
        );

        $lock->acquire(true);

        try {
            return $operation();
        } finally {
            $lock->release();
        }
    }
}

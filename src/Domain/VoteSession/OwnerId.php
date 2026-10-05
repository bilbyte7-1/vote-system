<?php

namespace App\Domain\VoteSession;

use InvalidArgumentException;

class OwnerId
{
    public function __construct(
        private string $ownerId
    )
    {
        if ($ownerId === '') {
            throw new InvalidArgumentException('UserId cannot be empty.');
        }
    }

    public function getId(): string
    {
        return $this->ownerId;
    }
}

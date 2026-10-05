<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\VoteSession;

use App\Domain\VoteSession\VoteSessionId;
use PHPUnit\Framework\TestCase;

final class VoteSessionIdTest extends TestCase
{
    public function testItReturnsProvidedId(): void
    {
        $id = new VoteSessionId('vote-session-id');

        self::assertSame('vote-session-id', $id->getId());
    }

    public function testItRejectsEmptyId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new VoteSessionId('');
    }
}

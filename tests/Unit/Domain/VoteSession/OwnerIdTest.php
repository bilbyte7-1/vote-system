<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\VoteSession;

use App\Domain\VoteSession\OwnerId;
use PHPUnit\Framework\TestCase;

final class OwnerIdTest extends TestCase
{
    public function testItReturnsProvidedId(): void
    {
        $id = new OwnerId('user-id');

        self::assertSame('user-id', $id->getId());
    }

    public function testItRejectsEmptyId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new OwnerId('');
    }
}

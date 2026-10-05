<?php

namespace App\Tests\Unit\Domain\User;

use App\Domain\User\UserId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UserIdTest extends TestCase
{
    public function testItCreatesUserId(): void
    {
        $userId = new UserId('user-id');

        self::assertSame(
            'user-id',
            $userId->getId(),
        );
    }

    public function testItRejectsEmptyUserId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('UserId cannot be empty.');

        new UserId('');
    }
}

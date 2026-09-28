<?php

namespace App\Tests\Domain\User;

use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\User;
use App\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testItCreatesUser(): void
    {
        $userId = new UserId('user-id');
        $email = new Email('user@example.com');
        $password = new Password('hashed-password');

        $user = new User(
            $userId,
            $email,
            $password
        );

        self::assertSame(
            $userId,
            $user->getId(),
        );

        self::assertSame(
            $email,
            $user->getEmail(),
        );

        self::assertSame(
            $password,
            $user->getPassword(),
        );
    }
}

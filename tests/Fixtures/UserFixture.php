<?php

namespace App\Tests\Fixtures;

use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\User;
use App\Domain\User\UserId;

class UserFixture
{
    public static function create(
        string $id = 'user-id',
        string $email = 'user@example.com',
        string $passwordHash = 'hashed-password',
    ): User {
        return new User(
            new UserId($id),
            new Email($email),
            new Password($passwordHash),
        );
    }
}

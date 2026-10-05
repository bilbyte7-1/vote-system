<?php

namespace App\Tests\Unit\Application\Registration;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Application\Registration\RegisterUserHandler;
use App\Application\Registration\RegisterUserRequest;
use App\Application\User\UserInitializer;
use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\User;
use App\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

final class RegisterUserHandlerTest extends TestCase
{
    public function testItRegistersUser(): void
    {
        $email = '1@2.com';
        $user = new User(
            new UserId('user-id'),
            new Email($email),
            new Password('hashed-password'),
        );

        $mockUserInitializer = $this->createMock(UserInitializer::class);
        $mockUserInitializer
            ->expects(self::once())
            ->method('initialize')
            ->with(
                self::identicalTo($email),
                self::identicalTo('plain-password')
            )
            ->willReturn($user);

        $mockUserPersister = $this->createMock(UserRepositoryInterface::class);
        $mockUserPersister
            ->expects(self::once())
            ->method('save')
            ->with(self::identicalTo($user));

        $handler = new RegisterUserHandler(
            $mockUserInitializer,
            $mockUserPersister
        );

        $request = new RegisterUserRequest(
            email: $email,
            plainPassword: 'plain-password',
        );

        $result = $handler->handle($request);

        self::assertSame(
            $result,
            $user
        );
    }

}

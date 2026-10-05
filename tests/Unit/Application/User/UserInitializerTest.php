<?php

namespace App\Tests\Unit\Application\User;

use App\Application\Interfaces\IdGeneratorInterface;
use App\Application\Interfaces\PasswordHasherInterface;
use App\Application\Interfaces\UserRepositoryInterface;
use App\Application\User\UserInitializer;
use PHPUnit\Framework\TestCase;

final class UserInitializerTest extends TestCase
{
    public function testItInitializesUser(): void
    {
        $idGenerator = $this->createStub(IdGeneratorInterface::class);
        $idGenerator
            ->method('generate')
            ->willReturn('user-id');

        $passwordHasher = $this->createMock(PasswordHasherInterface::class);
        $passwordHasher
            ->expects(self::once())
            ->method('hash')
            ->with('plain-password')
            ->willReturn('hashed-password');

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects(self::once())
            ->method('existsByEmail')
            ->with('  User@Example.COM  ')
            ->willReturn(false);

        $initializer = new UserInitializer(
            $idGenerator,
            $passwordHasher,
            $userRepository,
        );

        $user = $initializer->initialize(
            '  User@Example.COM  ',
            'plain-password',
        );

        self::assertSame(
            'user-id',
            $user->getId()->getId(),
        );

        self::assertSame(
            'user@example.com',
            $user->getEmail()->getEmail(),
        );

        self::assertSame(
            'hashed-password',
            $user->getPassword()->getPasswordHash(),
        );
    }
}

<?php

namespace App\Tests\Unit\Infrastructure;

use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Infrastructure\Security\SymfonyAuthenticationProvider;
use App\Tests\Fakers\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class SymfonyAuthenticationProviderTest extends TestCase
{
    public function testAuthenticateUser():void
    {
        $user = new User(
            new UserId('user-id'),
            new Email('user@example.com'),
            new Password('hashed-password'),
        );

        $plainPassword = 'strong-password';
        $userRepository = new InMemoryUserRepository();
        $userRepository->save($user);

        $mockPasswordHasher = $this->createMock(UserPasswordHasher::class);
        $mockPasswordHasher
            ->expects(self::once())
            ->method('isPasswordValid')
            ->with(
                self::isInstanceOf(PasswordAuthenticatedUserInterface::class),
                $plainPassword
            )
            ->willReturn(true);

        $symfonyAuthenticationProvider = new SymfonyAuthenticationProvider(
            $mockPasswordHasher,
            $userRepository
        );
        $authUser = $symfonyAuthenticationProvider->authenticate('user@example.com', $plainPassword);

        self::assertSame($user, $authUser);
    }
}

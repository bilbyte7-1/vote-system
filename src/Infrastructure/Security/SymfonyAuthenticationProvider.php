<?php

namespace App\Infrastructure\Security;

use App\Application\Interfaces\AuthenticationProviderInterface;
use App\Application\Interfaces\UserRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\DoctrineRepository\UserDoctrineRepository;
use App\Infrastructure\Persistance\Doctrine\UserDoctrineExtractor;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class SymfonyAuthenticationProvider implements AuthenticationProviderInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepositoryInterface      $userRepository,
    ) {
    }

    public function authenticate(
        string $email,
        string $plainPassword,
    ): User
    {
        $user = $this->userRepository->findByEmail($email);

        $symfonySecurityUser = new SymfonySecurityUser($user);

        if (!$this->passwordHasher->isPasswordValid(
            $symfonySecurityUser,
            $plainPassword,
        )) {
            throw new \InvalidArgumentException('Неверный пароль.');
        }

        return $user;
    }
}

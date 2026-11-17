<?php

namespace App\Application\User;

use App\Application\Exceptions\UserAlreadyExistsException;
use App\Application\Interfaces\IdGeneratorInterface;
use App\Application\Interfaces\UserRepositoryInterface;
use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Application\Interfaces\PasswordHasherInterface;

class UserInitializer
{
    public function __construct(
        private IdGeneratorInterface $idGenerator,
        private PasswordHasherInterface $passwordHasher,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function initialize(string $email, string $plainPassword): User
    {
        if ($this->userRepository->existsByEmail($email)) {
            throw new UserAlreadyExistsException();
        }
        return new User(
            new UserId($this->idGenerator->generate()),
            new Email($email),
            new Password($this->passwordHasher->hash($plainPassword)),
        );
    }
}

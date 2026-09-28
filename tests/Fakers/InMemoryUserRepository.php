<?php

namespace App\Tests\Fakers;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Domain\User\User;
use LogicException;
use App\Domain\User\UserId;

final class InMemoryUserRepository implements UserRepositoryInterface
{

    public function __construct(
        private ?User $savedUser = null,
    ) {
    }

    public function getSavedUser(): User
    {
        return $this->savedUser
            ?? throw new LogicException('No user has been saved.');
    }

    public function existsByEmail(string $email): bool
    {
        return $this->savedUser?->getEmail()->getEmail() === $email;
    }

    public function findByEmail(string $email): ?User
    {
        if ($this->savedUser?->getEmail()->getEmail() === $email) {
            return $this->savedUser;
        }

        return null;
    }

    public function save(User $user): void
    {
        $this->savedUser = $user;
    }

    public function findById(UserId $id): ?User
    {
        if ($this->savedUser->getId()->getId() === $id->getId())
        {
            return $this->savedUser;
        }
        return null;
    }
}

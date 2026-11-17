<?php

namespace App\Infrastructure\Security;

use App\Domain\User\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SymfonySecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private User $domainUser
    ) {
    }
    public function getRoles(): array
    {
        return [];
    }

    public function getUserIdentifier(): string
    {
        return $this->domainUser->getId()->getId();
    }

    public function getPassword(): ?string
    {
        return $this->domainUser->getPassword()->getPasswordHash();
    }

    public function eraseCredentials(): void
    {
    }
}

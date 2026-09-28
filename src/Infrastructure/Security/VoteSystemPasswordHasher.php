<?php

namespace App\Infrastructure\Security;

use App\Application\Interfaces\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface as SymfonyPasswordHasherInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;

class VoteSystemPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        #[Target('vote_system')]
        private readonly SymfonyPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function hash(string $plainPassword): string
    {
        return $this->passwordHasher->hash($plainPassword);
    }
}

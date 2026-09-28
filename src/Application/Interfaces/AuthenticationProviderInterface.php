<?php

namespace App\Application\Interfaces;

use App\AuthenticationFailedException;
use App\Domain\User\User;

interface AuthenticationProviderInterface
{
    /**
     * @throws AuthenticationFailedException
     */
    public function authenticate(
        string $email,
        string $plainPassword,
    ): User;
}

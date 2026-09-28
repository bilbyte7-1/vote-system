<?php

namespace App\Infrastructure\Security;

use App\Application\Interfaces\AccessTokenIssuerInterface;
use App\Domain\User\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class LexikAccessTokenIssuer implements AccessTokenIssuerInterface
{

    public function __construct(
        private JWTTokenManagerInterface $tokenManager,
    ) {
    }

    public function issue(User $user): string
    {
        $securityUser = new SymfonySecurityUser($user);
        return $this->tokenManager->create($securityUser);
    }
}

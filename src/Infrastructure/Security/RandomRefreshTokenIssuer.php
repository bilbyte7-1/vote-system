<?php

namespace App\Infrastructure\Security;

use App\Application\Interfaces\RefreshTokenIssuerInterface;

final class RandomRefreshTokenIssuer implements RefreshTokenIssuerInterface
{
    public function issue(): string
    {
        return bin2hex(random_bytes(32));
    }
}

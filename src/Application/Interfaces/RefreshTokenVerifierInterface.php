<?php

namespace App\Application\Interfaces;

use App\Domain\User\RefreshToken\RefreshToken;

interface RefreshTokenVerifierInterface
{
    public function verify(string $rawRefreshToken): Refreshtoken;
}

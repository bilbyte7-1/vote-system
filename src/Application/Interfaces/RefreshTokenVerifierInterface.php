<?php

namespace App\Application\Interfaces;

use App\Domain\RefreshToken\RefreshToken;

interface RefreshTokenVerifierInterface
{
    public function verify(string $rawRefreshToken): RefreshToken;
}

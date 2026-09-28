<?php

namespace App\Application\Interfaces;

interface RefreshTokenHasherInterface
{
    public function hash(string $refreshToken): string;
}

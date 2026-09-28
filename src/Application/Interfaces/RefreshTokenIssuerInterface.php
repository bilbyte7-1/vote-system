<?php

namespace App\Application\Interfaces;

interface RefreshTokenIssuerInterface
{
    public function issue(): string;
}

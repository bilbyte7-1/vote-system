<?php

namespace App\Infrastructure\Interfaces;

interface JwtGenerator
{
    public function generate(): string;
}

<?php

namespace App\Application\Interfaces;

interface PasswordHasherInterface
{
    public function hash(string $plainPassword): string;
}

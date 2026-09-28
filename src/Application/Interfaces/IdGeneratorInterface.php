<?php

namespace App\Application\Interfaces;

interface IdGeneratorInterface
{
    public function generate(): string;
}

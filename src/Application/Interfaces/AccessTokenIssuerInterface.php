<?php

namespace App\Application\Interfaces;

use App\Domain\User\User;

interface AccessTokenIssuerInterface
{
    public function issue(User $user): string;
}

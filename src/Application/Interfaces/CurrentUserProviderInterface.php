<?php

namespace App\Application\Interfaces;

interface CurrentUserProviderInterface
{
    public function getUserId(): UserId;
}

<?php

namespace App\Application\Interfaces;

use App\Domain\User\User;
use App\Domain\User\UserId;

interface UserRepositoryInterface
{
    public function existsByEmail(string $email): bool;
    public function findByEmail(string $email): ?User;
    public function save(User $user): void;

    public function findById(UserId $id): ?User;
}

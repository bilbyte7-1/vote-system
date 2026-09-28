<?php

namespace App\Domain\User;

readonly class User
{
    public function __construct(
        private UserId $id,
        private Email $email,
        private Password $password,
    ) {

    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }
}


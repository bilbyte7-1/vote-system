<?php

namespace App\Application\Registration;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Application\User\UserInitializer;
use App\Domain\User\User;

readonly class RegisterUserHandler
{
    public function __construct(
        private UserInitializer $userInitializer,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function handle(RegisterUserRequest $request): User
    {

        $user = $this->userInitializer->initialize(
            $request->email,
            $request->plainPassword
        );

        $this->userRepository->save($user);

        return $user;
    }
}

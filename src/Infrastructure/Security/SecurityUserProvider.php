<?php

namespace App\Infrastructure\Security;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Domain\User\UserId;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class SecurityUserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SymfonySecurityUser) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported user class "%s".',
                $user::class,
            ));
        }
        return $this->loadUserByIdentifier(
            $user->getUserIdentifier(),
        );
    }

    public function supportsClass(string $class): bool
    {
        return $class === SymfonySecurityUser::class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $userId = new UserId($identifier);
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            $exception = new UserNotFoundException();
            $exception->setUserIdentifier($identifier);

            throw $exception;
        }
        return new SymfonySecurityUser($user);
    }
}

<?php

namespace App\Infrastructure\DoctrineRepository;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Domain\User\Email;
use App\Domain\User\User;
use App\Domain\User\UserId;
use Doctrine\ORM\EntityManagerInterface;

class UserDoctrineRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
    public function existsByEmail(string $email): bool
    {
        return $this->entityManager
                ->createQueryBuilder()
                ->select('1')
                ->from(User::class, 'u')
                ->where('u.email = :email')
                ->setParameter('email', new Email($email), 'email')
                ->getQuery()
                ->getOneOrNullResult() !== null;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'email' => new Email($email),
            ]);
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findById(UserId $userId): ?User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'id' => $userId,
            ]);
    }
}

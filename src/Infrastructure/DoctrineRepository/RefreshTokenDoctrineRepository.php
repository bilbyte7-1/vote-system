<?php

namespace App\Infrastructure\DoctrineRepository;

use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Domain\RefreshToken\RefreshToken;
use Doctrine\ORM\EntityManagerInterface;

class RefreshTokenDoctrineRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(RefreshToken $refreshToken): void
    {
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();
    }

    public function findByHash(string $hash): ?RefreshToken
    {
        return $this->entityManager
            ->getRepository(RefreshToken::class)
            ->findOneBy([
                'tokenHash' => $hash,
            ]);
    }

    public function rotate(
        RefreshToken $refreshToken,
        RefreshToken $newRefreshToken,
    ): void {
        $this->entityManager->wrapInTransaction(
            function () use ($refreshToken, $newRefreshToken): void {
                $this->entityManager->persist($refreshToken);
                $this->entityManager->persist($newRefreshToken);
            }
        );
    }
}

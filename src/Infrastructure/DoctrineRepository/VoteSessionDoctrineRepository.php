<?php

namespace App\Infrastructure\DoctrineRepository;

use App\Application\Interfaces\VoteSessionRepositoryInterface;
use App\Domain\User\User;
use App\Domain\VoteSession\VoteSession;
use Doctrine\ORM\EntityManagerInterface;

class VoteSessionDoctrineRepository implements VoteSessionRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
    public function existsActiveVoteSession(): bool
    {
        return $this->entityManager
                ->createQueryBuilder()
                ->select('1')
                ->from(VoteSession::class, 'v')
                ->where('v.endedAt IS NULL')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult() !== null;
    }

    public function save(VoteSession $voteSession): void
    {
        $this->entityManager->persist($voteSession);
        $this->entityManager->flush();
    }
}

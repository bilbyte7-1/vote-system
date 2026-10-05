<?php

namespace App\Application\VoteSessionStart;

use App\Application\Exceptions\ActiveVoteSessionAlreadyExists;
use App\Application\Interfaces\VoteSessionRepositoryInterface;
use App\Application\Interfaces\VoteSessionStartLockInterface;
use App\Application\VoteSession\VoteSessionInitializer;
use App\Domain\VoteSession\VoteSessionId;

class VoteSessionStartHandler
{
    public function __construct(
        private VoteSessionInitializer $voteSessionInitializer,
        private VoteSessionStartLockInterface $startLock,
        private VoteSessionRepositoryInterface $voteSessionRepository,
    )
    {}
    public function handle(VoteSessionStartRequest $voteSessionStartRequest): VoteSessionId
    {
        $firstPhaseDuration = $voteSessionStartRequest->firstDuration;
        $secondPhaseDuration = $voteSessionStartRequest->secondDuration;
        $ownerId = $voteSessionStartRequest->userId;

        return $this->startLock->execute(
            function () use (
                $ownerId,
                $firstPhaseDuration,
                $secondPhaseDuration,
            ): VoteSessionId {
                if ($this->voteSessionRepository->existsActiveVoteSession()) {
                    throw new ActiveVoteSessionAlreadyExists();
                }

                $voteSession = $this->voteSessionInitializer->initialize(
                    $ownerId,
                    [],
                    $firstPhaseDuration,
                    $secondPhaseDuration,
                );

                $this->voteSessionRepository->save($voteSession);

                return $voteSession->getId();
            }
        );
    }
}

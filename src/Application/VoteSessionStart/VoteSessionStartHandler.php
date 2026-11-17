<?php

namespace App\Application\VoteSessionStart;

use App\Application\Exceptions\ActiveVoteSessionAlreadyExists;
use App\Application\Interfaces\NotificationPublisherInterface;
use App\Application\Interfaces\VoteSessionRepositoryInterface;
use App\Application\Interfaces\VoteSessionStartLockInterface;
use App\Application\Messages\VoteSessionStarted;
use App\Application\VoteSession\VoteSessionInitializer;
use App\Domain\VoteSession\VoteSessionId;

class VoteSessionStartHandler
{
    public function __construct(
        private VoteSessionInitializer $voteSessionInitializer,
        private VoteSessionStartLockInterface $startLock,
        private VoteSessionRepositoryInterface $voteSessionRepository,
        private NotificationPublisherInterface $notificationPublisher,
    ) {
    }
    public function handle(VoteSessionStartRequest $voteSessionStartRequest): VoteSessionId
    {
        $firstPhaseDuration = $voteSessionStartRequest->firstDuration;
        $secondPhaseDuration = $voteSessionStartRequest->secondDuration;
        $ownerId = $voteSessionStartRequest->userId;

        $voteSessionId =  $this->startLock->execute(
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

        $this->notificationPublisher->notify(
            new VoteSessionStarted($voteSessionId),
        );
        return $voteSessionId;
    }
}

<?php

namespace App\Application\Messages;

use App\Application\Interfaces\RealtimePublisherInterface;

final class VoteSessionStartedHandler
{
    public function __construct(
        private RealtimePublisherInterface $realtimePublisher,
    ) {
    }

    public function __invoke(VoteSessionStarted $message): void
    {
        $this->realtimePublisher->publish(
            $message::TYPE,
            [
                'voteSessionId' => (string) $message->voteSessionId,
            ],
        );
    }
}

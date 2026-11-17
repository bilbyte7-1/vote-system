<?php

namespace App\Infrastructure\Realtime;

use App\Application\Interfaces\RealtimePublisherInterface;

final class RedisRealtimePublisher implements RealtimePublisherInterface
{
    private const CHANNEL = 'realtime.events';

    public function __construct(
        private \Redis $redis,
    ) {
    }

    public function publish(string $event, array $payload): void
    {
        $message = json_encode(
            [
                'event' => $event,
                'payload' => $payload,
            ],
            JSON_THROW_ON_ERROR,
        );

        $this->redis->publish(
            self::CHANNEL,
            $message,
        );
    }
}

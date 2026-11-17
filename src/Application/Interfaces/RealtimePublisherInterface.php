<?php

namespace App\Application\Interfaces;

interface RealtimePublisherInterface
{
    public function publish(
        string $event,
        array $payload
    ): void;
}

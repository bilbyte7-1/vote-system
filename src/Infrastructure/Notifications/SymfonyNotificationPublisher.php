<?php

namespace App\Infrastructure\Notifications;

use App\Application\Interfaces\ApplicationMessageInterface;
use App\Application\Interfaces\NotificationPublisherInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyNotificationPublisher implements NotificationPublisherInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function notify(ApplicationMessageInterface $message): void
    {
        $this->messageBus->dispatch($message);
    }
}

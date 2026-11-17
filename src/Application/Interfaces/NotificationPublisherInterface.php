<?php

namespace App\Application\Interfaces;

interface NotificationPublisherInterface
{
    public function notify(ApplicationMessageInterface $message): void;
}

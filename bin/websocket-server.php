<?php

use Workerman\Redis\Client;
use Workerman\Worker;

require_once __DIR__ . '/../vendor/autoload.php';

$websocket = new Worker('websocket://0.0.0.0:8081');

// Пока один процесс.
// Тогда все WebSocket connections находятся в одном Worker.
$websocket->count = 1;

$websocket->onWorkerStart = function (Worker $worker): void {
    $redis = new Client('redis://redis:6379');

    $redis->subscribe(
        ['realtime.events'],
        function (string $channel, string $message) use ($worker): void {
            foreach ($worker->connections as $connection) {
                $connection->send($message);
            }
        },
    );
};

$websocket->onConnect = function (): void {
    echo "WebSocket client connected\n";
};

$websocket->onClose = function (): void {
    echo "WebSocket client disconnected\n";
};

// Пока сообщения от клиента нам не нужны.
$websocket->onMessage = function (): void {
};

Worker::runAll();

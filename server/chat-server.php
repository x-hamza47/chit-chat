<?php

use Workerman\Worker;
use Workerman\Connection\TcpConnection;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ChatHandler.php';

// ? WebSocket server on port 2346
$ws_worker = new Worker("websocket://0.0.0.0:2346");
$ws_worker->count = 1;

$chatHandler = new ChatHandler();

$ws_worker->onConnect = function (TcpConnection $connection) {
    echo "New connection: {$connection->id}\n";
};

$ws_worker->onMessage = function (TcpConnection $connection, $data) use ($chatHandler) {
    $chatHandler->onMessage($connection, $data);
};

$ws_worker->onClose = function ($connection) use ($chatHandler) {
    $chatHandler->onClose($connection);
};

Worker::runAll();

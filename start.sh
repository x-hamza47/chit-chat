#!/bin/sh

echo "Starting PHP web server..."
php -S 0.0.0.0:$PORT &

echo "Starting Workerman..."
php server/chat-server.php start
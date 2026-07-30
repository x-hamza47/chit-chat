#!/bin/sh

echo "Generating nginx config..."
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

echo "Starting PHP server..."
php -S 127.0.0.1:8000 &

echo "Starting Workerman..."
php server/chat-server.php start &

echo "Starting Nginx..."
exec nginx -g "daemon off;"
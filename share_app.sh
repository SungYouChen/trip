#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$DIR"

echo " stopping any existing server..."
pkill -f "php artisan serve"

echo "🚀 Starting App..."
nohup php artisan serve > /dev/null 2>&1 &
PHP_PID=$!

echo "⏳ Waiting for server..."
sleep 3

echo "🌐 Starting Permanent Cloudflare Tunnel..."
echo "-----------------------------------------------------"
echo "👉 Your Permanent URL: https://trip.elk-web.com"
echo "-----------------------------------------------------"
/opt/homebrew/bin/cloudflared tunnel --config ./cloudflared_config.yml run japan-trip

# Cleanup on exit
kill $PHP_PID

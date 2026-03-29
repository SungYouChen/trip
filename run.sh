#!/bin/bash
# Get the directory where the script is located
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$DIR"

echo "Starting Elk & Winnie's Japan Trip App..."

# Get Local IP (Try getting Wi-Fi IP on Mac)
IP=$(ipconfig getifaddr en0)
if [ -z "$IP" ]; then
    IP=$(ipconfig getifaddr en1)
fi

echo "-----------------------------------------------------"
echo "💻 Local Access (This Mac): http://127.0.0.1:8000"
if [ ! -z "$IP" ]; then
    echo "📱 Phone Access (Same Wi-Fi): http://$IP:8000"
fi
echo "-----------------------------------------------------"

echo "Opening browser..."
# Try to open browser after a slight delay (background)
(sleep 2 && open "http://127.0.0.1:8000") &

# Start the server on 0.0.0.0 to allow network access
php artisan serve --host 0.0.0.0

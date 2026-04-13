#!/bin/sh

set -eu

if [ "${APP_ENV:-local}" = "production" ]; then
    echo "Starting production server..."
    exec php public/index.php --host=0.0.0.0 --port="${APP_PORT:-1337}"
fi

echo "Starting development server with file watcher..."
exec php ./server --host=0.0.0.0 --port="${APP_PORT:-1337}"

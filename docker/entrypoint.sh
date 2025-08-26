#!/bin/sh

set -e

if [ "$APP_ENV" = "production" ]; then
    echo "Starting production server..."
    php public/index.php --host=0.0.0.0 --port=${APP_PORT:-1337}
else
    echo "Starting development server with file watcher..."
    php ./server --host=0.0.0.0 --port=${APP_PORT:-1337}
fi
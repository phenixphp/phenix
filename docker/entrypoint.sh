#!/bin/bash

set -e

chmod +x ./phenix

if [ "$APP_ENV" != "production" ]; then
    echo "Waiting for MySQL to be ready..."
    while ! nc -z mysql 3306; do
        sleep 1
    done
    echo "MySQL is ready!"
fi

if [ "$APP_ENV" = "development" ]; then
    echo "Running migrations..."
    ./phenix migrate:run || true
fi

if [ "$APP_ENV" = "production" ]; then
    echo "Starting production server..."
    php public/index.php --host=0.0.0.0 --port=${APP_PORT:-1337}
else
    echo "Starting development server with file watcher..."
    php ./server --host=0.0.0.0 --port=${APP_PORT:-1337}
fi
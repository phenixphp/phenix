#!/bin/sh

if [ "$APP_ENV" = "production" ]; then
    php public/index.php --host=0.0.0.0 --port=${APP_PORT}
else
    php ./server --host=0.0.0.0 --port=${APP_PORT}
fi
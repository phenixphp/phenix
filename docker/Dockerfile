FROM serversideup/php:8.2-cli-alpine AS base

USER root

RUN apk add --no-cache \
    curl \
    git \
    unzip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM base AS local

RUN apk add --no-cache nodejs npm

COPY --chown=www-data:www-data . /var/www/html

RUN chmod -R 755 /var/www/html/storage

USER www-data

RUN composer install --no-scripts --no-autoloader
RUN composer dump-autoload --optimize

EXPOSE ${APP_PORT:-1337}

ENTRYPOINT ["docker/entrypoint.sh"]

FROM base AS production

COPY --chown=www-data:www-data . /var/www/html

RUN chmod -R 755 /var/www/html/storage

USER www-data

RUN composer install --no-dev --optimize-autoloader --no-scripts

EXPOSE ${APP_PORT:-1337}

ENTRYPOINT ["docker/entrypoint.sh"]

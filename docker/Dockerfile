FROM serversideup/php:8.2-cli-alpine@sha256:33dbb93850850fd6db1e4e06f3150f170c243c4af8ecfd73aa21d9c41bc2cbb6 AS php-ext-builder

USER root

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers sqlite-dev \
    && docker-php-ext-install pcntl sockets sqlite3 \
    && docker-php-ext-enable pcntl sockets sqlite3 \
    && apk del .build-deps

FROM composer:2@sha256:c34b41a6e88e7b84bb5411ed338953350da691e5324a449fc384d39ee0e53e64 AS composer-bin

FROM php-ext-builder AS vendor-dev

USER root

COPY --from=composer-bin /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN apk add --no-cache git unzip

COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-scripts \
    --no-plugins \
    --no-autoloader

COPY . .

RUN composer dump-autoload --optimize

FROM php-ext-builder AS vendor-production

USER root

COPY --from=composer-bin /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN apk add --no-cache git unzip

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-scripts \
    --no-plugins \
    --no-autoloader

COPY . .

RUN composer dump-autoload --optimize

FROM serversideup/php:8.2-cli-alpine@sha256:33dbb93850850fd6db1e4e06f3150f170c243c4af8ecfd73aa21d9c41bc2cbb6 AS runtime-base

USER root

WORKDIR /var/www/html

ENV APP_PORT=1337

RUN apk add --no-cache sqlite-libs

COPY --from=php-ext-builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-ext-builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD ["php", "-r", "$$port=(int) (getenv('APP_PORT') ?: 1337); $$socket=@fsockopen('127.0.0.1', $$port, $$errno, $$errstr, 2); if (!$$socket) { fwrite(STDERR, $$errstr ?: 'connection failed'); exit(1); } fclose($$socket);"]

EXPOSE 1337

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]

FROM runtime-base AS local

USER root

RUN apk add --no-cache nodejs npm

COPY --chown=www-data:www-data . /var/www/html
COPY --from=vendor-dev --chown=www-data:www-data /var/www/html/vendor /var/www/html/vendor

RUN mkdir -p /var/www/html/storage/framework/logs /var/www/html/storage/framework/views /var/www/html/storage/framework/testing \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/database /var/www/html/vendor /var/www/html/docker/entrypoint.sh \
    && chmod -R u=rwX,g=rX,o=rX /var/www/html/storage /var/www/html/database \
    && chmod 750 /var/www/html/docker/entrypoint.sh

USER www-data

FROM runtime-base AS production

USER root

ENV APP_ENV=production

COPY --chown=www-data:www-data . /var/www/html
COPY --from=vendor-production --chown=www-data:www-data /var/www/html/vendor /var/www/html/vendor

RUN mkdir -p /var/www/html/storage/framework/logs /var/www/html/storage/framework/views /var/www/html/storage/framework/testing \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/database /var/www/html/vendor /var/www/html/docker/entrypoint.sh \
    && chmod -R u=rwX,g=rX,o=rX /var/www/html/storage /var/www/html/database \
    && chmod 750 /var/www/html/docker/entrypoint.sh

USER www-data

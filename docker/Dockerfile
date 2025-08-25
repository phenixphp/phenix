FROM php:8.2-cli AS base

WORKDIR /var/www/

RUN apt-get update \
    && apt-get install -y curl git unzip \
    && docker-php-ext-install pcntl pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM base AS local

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

COPY . /var/www/

RUN composer install --no-scripts --no-autoloader

RUN composer dump-autoload --optimize

EXPOSE ${APP_PORT:-1337}

ENTRYPOINT ["docker/entrypoint.sh"]

FROM base AS production

COPY . /var/www/

RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

USER www-data

EXPOSE ${APP_PORT:-1337}

ENTRYPOINT ["docker/entrypoint.sh"]

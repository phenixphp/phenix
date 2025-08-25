FROM php:8.2-cli AS development

WORKDIR /var/www/

RUN apt-get update \
    && apt-get install -y curl \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pcntl pdo pdo_mysql

COPY . /var/www/

FROM php:8.2-cli AS production

WORKDIR /var/www/

RUN docker-php-ext-install pcntl pdo pdo_mysql

COPY . /var/www/

USER www-data

EXPOSE 8080

ENTRYPOINT ["docker/entrypoint.sh"]

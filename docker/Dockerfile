FROM php:8.2-cli
WORKDIR /usr/src/phenix

ARG USER_UID
ARG USER_GID

RUN groupadd -g ${USER_GID} phenix_group && useradd -ms /bin/bash -u ${USER_UID} -g ${USER_GID} phenix_user

RUN docker-php-ext-install pcntl pdo pdo_mysql

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

USER phenix_user

COPY . /usr/src/phenix

ENV APP_PORT=${APP_PORT}

EXPOSE ${APP_PORT}

ENTRYPOINT ["docker/entrypoint.sh"]

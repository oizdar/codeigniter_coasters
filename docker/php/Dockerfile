FROM php:8.3-fpm
ARG APP_ENV
ARG UID
ARG GID

COPY . /var/www

RUN groupmod -g $GID www-data && \
    usermod -u $UID -g $GID www-data

WORKDIR /var/www

RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    curl \
    libicu-dev

RUN docker-php-ext-install intl
RUN pecl install --onlyreqdeps --force redis

RUN docker-php-ext-enable intl \
    redis

USER www-data
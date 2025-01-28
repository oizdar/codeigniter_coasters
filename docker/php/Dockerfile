FROM php:8.3-fpm
ARG APP_ENV

COPY . /var/www

RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/writable

WORKDIR /var/www


RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    curl \
    libicu-dev

RUN docker-php-ext-install intl
RUN docker-php-ext-enable intl

RUN pecl install --onlyreqdeps --force redis \
&& rm -rf /tmp/pear \
&& docker-php-ext-enable redis

RUN mv .env.${APP_ENV} .env

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

EXPOSE 9000
CMD ["php-fpm"]
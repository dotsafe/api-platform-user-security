ARG PHP_VERSION=7.1
FROM php:$PHP_VERSION

ARG XDEBUG_VERSION=2.9.8

RUN apt-get update && apt-get install -y \
    libicu-dev \
    git \
    wget \
    zip \
    curl \
    libcurl4-openssl-dev \
    libmcrypt-dev \
    libzip-dev

RUN docker-php-ext-install curl pdo \
    && docker-php-ext-enable opcache

# apcu
RUN pecl install apcu \
    && echo "extension=apcu.so" > $PHP_INI_DIR/conf.d/apcu.ini

RUN pecl install xdebug-$XDEBUG_VERSION \
    && pecl install pcov \
    && docker-php-ext-enable pcov

# Composer
RUN curl -sS https://getcomposer.org/download/2.1.6/composer.phar -o /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

WORKDIR /code

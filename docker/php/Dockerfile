FROM php:8.1-fpm

ENV TERM xterm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libmemcached-dev \
    curl \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    vim \
    zlib1g-dev libicu-dev g++ \
    --no-install-recommends \
    libzip-dev zip \
    && rm -r /var/lib/apt/lists/*

# configure gd library
RUN docker-php-ext-configure gd \
    --with-jpeg \
    --with-freetype

# configure intl
RUN docker-php-ext-configure intl

# Install mongodb, xdebug
RUN pecl install mongodb \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install extensions using the helper script provided by the base image
RUN docker-php-ext-install \
    bcmath \
    pdo_mysql \
    pdo_pgsql \
    gd \
    intl \
    zip

RUN usermod -u 1000 www-data

WORKDIR /var/www/laravel

ADD ./docker/php/laravel.ini /usr/local/etc/php/conf.d
ADD ./docker/php/laravel.pool.conf /usr/local/etc/php-fpm.d/

CMD ["php-fpm"]

EXPOSE 9000
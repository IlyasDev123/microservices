FROM php:8.4-fpm

LABEL maintainer="Muhammad Ilyas"

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
    pdo_mysql \
    mysqli \
    bcmath \
    intl \
    zip \
    sockets \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

COPY . .

RUN chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["php-fpm"]
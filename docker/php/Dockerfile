FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        libzip-dev \
        zip \
	&& docker-php-ext-configure zip \
	&& docker-php-ext-install zip \
	&& docker-php-ext-install mysqli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
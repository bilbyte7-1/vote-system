FROM php:8.4-fpm

# system deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    librabbitmq-dev \
    && docker-php-ext-install \
         pdo \
         pdo_pgsql \
         intl \
         opcache \
         pcntl \
         posix \
    && pecl install amqp-2.2.0 \
    && docker-php-ext-enable amqp \
    && pecl install redis \
    && docker-php-ext-enable redis

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

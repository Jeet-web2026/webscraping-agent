FROM php:8.3-fpm

# Install system dependencies needed to build gd
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# ... rest of your existing ext installs (pdo_mysql, redis, sodium, etc.)
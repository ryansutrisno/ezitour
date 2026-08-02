# Multi-stage Docker build for Laravel 12 + Vite + Nginx + PHP 8.4

# Stage 1: Build Node/Vite assets
FROM node:20-alpine AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Install Composer production dependencies
FROM composer:2.8 AS composer-builder
WORKDIR /app
COPY composer*.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --no-dev --optimize

# Stage 3: Running Environment
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# Install required system packages
RUN apk add --no-cache \
    nginx \
    bash \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    mysql-client

# Configure & install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        pdo_mysql \
        mbstring \
        xml

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy application files and build artifacts
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=assets-builder /app/public/build ./public/build
COPY --chown=www-data:www-data --from=composer-builder /app/vendor ./vendor

# Setup Nginx directories and permissions
RUN mkdir -p /run/nginx /var/log/nginx \
    && chown -R www-data:www-data /run/nginx /var/log/nginx /var/lib/nginx \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]

# BookingFlow - Production Dockerfile
# Multi-stage build for optimized production image

# ============================================================================
# Stage 1: Build frontend assets
# ============================================================================
FROM node:20-alpine AS frontend-builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci --only=production

# Copy source files
COPY . .

# Build frontend assets
RUN npm run build

# ============================================================================
# Stage 2: PHP Dependencies
# ============================================================================
FROM composer:2.7 AS composer-builder

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (production only, optimized)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ============================================================================
# Stage 3: Production Runtime
# ============================================================================
FROM php:8.3-fpm-alpine

LABEL maintainer="BookingFlow DevOps"
LABEL version="1.0.0"
LABEL description="BookingFlow Laravel Application - Production Image"

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    # System utilities
    bash \
    curl \
    git \
    unzip \
    zip \
    # Image processing
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    # Database clients
    mysql-client \
    postgresql-client \
    # Redis
    redis \
    # Supervisor for process management
    supervisor \
    # Nginx for serving
    nginx \
    # Required libraries
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    libxml2-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        soap \
        opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install APCu for additional caching
RUN pecl install apcu && docker-php-ext-enable apcu

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/bookingflow.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copy Nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copy Supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

# Copy vendor from composer stage
COPY --from=composer-builder --chown=www-data:www-data /app/vendor /var/www/html/vendor

# Copy built frontend assets from frontend stage
COPY --from=frontend-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

# Create required directories and set permissions
RUN mkdir -p \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
    && chmod -R 775 \
        storage \
        bootstrap/cache

# Create Nginx runtime directories
RUN mkdir -p /var/run/nginx \
    && chown -R www-data:www-data /var/run/nginx \
    && chown -R www-data:www-data /var/lib/nginx

# Optimize Laravel
RUN php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Expose port 80 for Nginx
EXPOSE 80

# Switch to www-data user
USER www-data

# Start supervisor which will manage Nginx, PHP-FPM, and Laravel queue workers
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

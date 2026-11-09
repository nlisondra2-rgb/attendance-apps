# ==============================================================================
# Stage 1: Build Frontend Assets with Node
# ==============================================================================
FROM node:20-alpine AS frontend
WORKDIR /app

# Copy dependency files first for layer caching
COPY package*.json ./
RUN npm install

# Copy application files and build Vite bundles
COPY . .
RUN npm run build

# ==============================================================================
# Stage 2: PHP 8.2 + Apache Production Image
# ==============================================================================
FROM php:8.2-apache

# Install required system packages and PHP extension libraries
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libsqlite3-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy compiled frontend assets from Stage 1 into public/build
COPY --from=frontend /app/public/build ./public/build

# Copy custom Apache virtual host configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Fix Windows CRLF line endings on the entrypoint script and grant execution permissions
RUN sed -i -e 's/\r$//' /var/www/html/docker-entrypoint.sh \
    && cp /var/www/html/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose Render standard port
EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]


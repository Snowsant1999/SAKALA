# ----------------------------------------------------
# Stage 1: Build Frontend Assets (Vite + Tailwind v4)
# ----------------------------------------------------
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci --ignore-scripts || npm install
COPY . .
RUN npm run build

# ----------------------------------------------------
# Stage 2: PHP 8.3 Apache Backend
# ----------------------------------------------------
FROM php:8.3-apache

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build /var/www/html/public/build

# Copy Apache configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Prepare permissions for root & non-root containers (e.g. Hugging Face Spaces UID 1000)
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/run/apache2 /var/lock/apache2 /var/log/apache2 \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/run/apache2 /var/lock/apache2 /var/log/apache2

# Copy and set entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Environment Defaults (Hugging Face Spaces default: 7860)
ENV PORT=7860
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

EXPOSE 7860

ENTRYPOINT ["docker-entrypoint.sh"]

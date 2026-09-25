#!/bin/sh
set -e

# Configure port dynamically (Render / Railway / Fly.io injects $PORT)
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true

# Ensure permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create SQLite database directory & file if needed
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/database

# Check if .env exists, if not copy from example
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Ensure APP_KEY is set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear and optimize Laravel caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

exec apache2-foreground

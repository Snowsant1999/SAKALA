#!/bin/sh
set -e

# Default port to 7860 (Hugging Face Spaces standard) or dynamically from $PORT
PORT=${PORT:-7860}

# Update Apache listening port
sed -i "s/Listen [0-9]*/Listen $PORT/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:*>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true

# Ensure permissions for Laravel & Apache
mkdir -p /var/www/html/storage/framework/cache/data /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/logs /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database 2>/dev/null || true
chmod -R 777 /var/run/apache2 /var/lock/apache2 /var/log/apache2 2>/dev/null || true

# Create SQLite database file if needed
touch /var/www/html/database/database.sqlite
chmod 777 /var/www/html/database/database.sqlite 2>/dev/null || true

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

echo "Starting server on port $PORT..."
exec apache2-foreground

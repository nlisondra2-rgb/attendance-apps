#!/bin/bash
set -e

# Default PORT to 80 if not provided by Render (Render passes PORT=10000)
export PORT="${PORT:-80}"

# Ensure Apache listens on $PORT
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/*.conf 2>/dev/null || true

# If using SQLite and file does not exist, initialize it
if [ "${DB_CONNECTION}" = "sqlite" ] || [ -z "${DB_CONNECTION}" ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
fi

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Optimize Laravel cache for production if APP_KEY is set
if [ -n "$APP_KEY" ]; then
    echo "Caching configuration, routes, and views..."
    php artisan config:clear || true
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration skipped or failed (check database connection settings)"
else
    echo "WARNING: APP_KEY is not set. Please set APP_KEY in Render environment variables."
fi

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground


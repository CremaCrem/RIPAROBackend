#!/usr/bin/env bash
set -e

echo "Optimizing app caches..."
php artisan optimize

echo "Running migrations..."
php artisan migrate --force || true

echo "Ensuring storage symlink..."
php artisan storage:link || true

echo "Starting PHP-FPM and Nginx..."
php-fpm &
exec nginx -g "daemon off;"
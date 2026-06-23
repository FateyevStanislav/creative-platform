#!/bin/bash

set -e

mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/testing
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

if [ ! -f /var/www/html/.initialized ]; then
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
    php artisan storage:link --force || true
    touch /var/www/html/.initialized
fi

exec "$@"
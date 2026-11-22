#!/usr/bin/env bash
set -e

mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan storage:link || true
php artisan migrate --force || true
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

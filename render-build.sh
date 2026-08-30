#!/usr/bin/env bash

set -e

echo "=== Installing PHP dependencies ==="
composer install --no-dev --optimize-autoloader

echo "=== Installing Node dependencies ==="
npm install

echo "=== Building assets ==="
npm run build

echo "=== Setting up environment ==="
cp .env.example .env
php artisan key:generate --force

echo "=== Caching config/routes/views ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Setting storage permissions ==="
chmod -R 775 storage bootstrap/cache

echo "=== Build complete! ==="

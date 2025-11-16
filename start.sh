#!/usr/bin/env bash
# Exit on first error
set -e

echo "🔹 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader || true

echo "🔹 Clearing caches..."
php artisan optimize:clear || true

echo "🔹 Creating storage link..."
php artisan storage:link || true

echo "🔹 Caching configuration..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "🔹 Running migrations (safe mode)..."
php artisan migrate --force || true

echo "✅ Starting Laravel on Render's assigned port..."
php artisan serve --host=0.0.0.0 --port=$PORT

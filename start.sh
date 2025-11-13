#!/usr/bin/env bash
set -o errexit

# 🧹 Clear any cached files
php artisan optimize:clear || true

# 🧱 Run migrations (safe mode)
php artisan migrate --force || true

# 🔗 Create storage link
php artisan storage:link || true

# 🚀 Optimize Laravel for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 🌐 Start Laravel on Render's assigned port
php artisan serve --host=0.0.0.0 --port=$PORT

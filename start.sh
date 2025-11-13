#!/usr/bin/env bash
set -o errexit

# 1️⃣ Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# 2️⃣ Run migrations safely
php artisan migrate --force

# 3️⃣ Clear caches and optimize
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4️⃣ Start Laravel
php artisan serve --host 0.0.0.0 --port $PORT

#!/bin/sh
set -e

# Ensure Composer dependencies are installed before Laravel runs
if [ ! -d "vendor" ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate app key if missing
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set — generating temporary key"
  php artisan key:generate --force
fi

# Wait for DB to be ready
echo "Waiting for DB..."
n=0
until php -r "new PDO('pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0);" 2>/dev/null || [ $n -gt 60 ]
do
  n=$((n+1))
  echo "  waiting ($n)..."
  sleep 1
done

# Run migrations
php artisan migrate --force || echo "Migration failed but continuing..."

# Seed admin
php artisan db:seed --class=AdminSeeder --force || echo "AdminSeeder failed but continuing..."

# Cache
php artisan config:clear || true
php artisan config:cache || true

# Start Laravel
php artisan serve --host=0.0.0.0 --port=8000

#!/bin/sh
set -e

# If APP_KEY not set, generate one
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

# ✅ Run all pending migrations (safe for existing tables)
echo "Running database migrations..."
php artisan migrate --force || echo "Migrations failed, continuing startup"

# ✅ Optionally re-seed admin user if needed
php artisan db:seed --class=AdminSeeder --force || echo "Admin seeder failed"

# Clear and cache config
php artisan config:clear || true
php artisan config:cache || true

# Start Laravel app
php artisan serve --host=0.0.0.0 --port=8000
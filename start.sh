#!/bin/sh
set -e

# If APP_KEY not set, try to generate (prints valid key but we prefer env variable)
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set — generating temporary key"
  php artisan key:generate --force
fi

# Wait for DB to be ready (tries for ~60 attempts)
echo "Waiting for DB..."
n=0
until php -r "new PDO('pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0);" 2>/dev/null || [ $n -gt 60 ]
do
  n=$((n+1))
  echo "  waiting ($n)..."
  sleep 1
done

# Run migrations and seed admin user (safe: will not stop container if fail)
php artisan migrate --force || echo "migrate failed"
php artisan db:seed --class=AdminSeeder --force || echo "db:seed AdminSeeder failed"


#!/bin/bash
php artisan key:generate --force
php artisan migrate --force
php artisan config:cache
php artisan serve --host=0.0.0.0 --port=8000

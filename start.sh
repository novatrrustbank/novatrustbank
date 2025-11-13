#!/usr/bin/env bash
set -e

echo "=== Starting Laravel container ==="

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set — generating temporary key..."
  php artisan key:generate --force || true
fi

# Wait for PostgreSQL to be ready
echo "⏳ Waiting for database connection..."
tries=0
until php -r "
try {
    new PDO(
        'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" >/dev/null 2>&1 || [ "$tries" -ge 60 ]; do
  tries=$((tries+1))
  echo "  Waiting for DB... ($tries)"
  sleep 1
done

# Run migrations (force to skip confirmation)
echo "🚀 Running migrations..."
php artisan migrate --force || echo "Migration failed — continuing startup."

# Optionally seed admin
echo "🌱 Seeding admin (if exists)..."
php artisan db:seed --class=AdminSeeder --force || echo "AdminSeeder failed — continuing."

# Cache configs
php artisan config:clear || true
php artisan config:cache || true

# Start Laravel server
echo "✅ Starting Laravel on port 8000..."
php artisan serve --host=0.0.0.0 --port=8000
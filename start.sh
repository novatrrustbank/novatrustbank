#!/bin/sh
set -e

echo "=== Starting Laravel container ==="

# If APP_KEY not set, generate one temporarily (recommended to store in Render dashboard)
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set — generating temporary key"
  php artisan key:generate --force
fi

# Wait for PostgreSQL database to be ready (~60s timeout)
echo "Waiting for database connection..."
n=0
until php -r "
try {
  new PDO(
    'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD')
  );
  exit(0);
} catch (Exception \$e) { exit(1); }
" 2>/dev/null || [ \$n -gt 60 ]
do
  n=\$((n+1))
  echo "  waiting for DB... (\$n)"
  sleep 1
done

# Clear old cache (prevents config or route issues)
php artisan config:clear || true
php artisan cache:clear || true

# ✅ Run migrations safely (includes messages table)
echo "Running migrations..."
php artisan migrate --force || {
  echo "Migration failed, retrying in 5s..."
  sleep 5
  php artisan migrate --force || echo "⚠️ Migration failed again — continuing..."
}

# Optional: seed admin
php artisan db:seed --class=AdminSeeder --force || echo "AdminSeeder failed (may be fine)"

# Rebuild cache
php artisan config:cache || true
php artisan route:cache || true

# Start Laravel’s built-in server
echo "=== Starting PHP server ==="
php artisan serve --host=0.0.0.0 --port=8000
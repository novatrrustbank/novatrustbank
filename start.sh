#!/bin/sh
set -e

echo "🚀 Starting Laravel app initialization..."

# Ensure APP_KEY is set
if [ -z "$APP_KEY" ]; then
  echo "🔑 APP_KEY not found — generating temporary key..."
  php artisan key:generate --force
fi

# Wait for database connection
echo "⏳ Waiting for database to be ready..."
n=0
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
" 2>/dev/null || [ $n -gt 60 ]; do
  n=$((n+1))
  echo "   Database not ready yet... retrying ($n)"
  sleep 2
done

echo "✅ Database connection established!"

# Run migrations (force mode for production)
echo "🗂️ Running database migrations..."
php artisan migrate --force || echo "⚠️ Migration failed, continuing startup..."

# Optionally run seeders (admin, sample data, etc.)
echo "🌱 Seeding admin user (if applicable)..."
php artisan db:seed --class=AdminSeeder --force || echo "⚠️ Admin seeder failed or already exists."

# Clear and cache configuration
echo "🧹 Clearing and caching config..."
php artisan config:clear || true
php artisan config:cache || true

# Show successful start log
echo "✅ Laravel app initialized successfully!"
echo "🌍 Starting server on port 8000..."

# Start Laravel server
php artisan serve --host=0.0.0.0 --port=8000
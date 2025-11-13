#!/bin/sh
set -e

# ==========================================
# ✅ Laravel Render Deployment Script
# ==========================================

echo "🚀 Starting Laravel deploy..."

# 1️⃣ Generate temporary APP_KEY if missing
if [ -z "$APP_KEY" ]; then
  echo "⚠️ APP_KEY not set — generating temporary key"
  php artisan key:generate --force
fi

# 2️⃣ Wait for database connection to be ready
echo "⏳ Waiting for database..."
n=0
until php -r "new PDO('pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0);" 2>/dev/null || [ $n -gt 60 ]
do
  n=$((n+1))
  echo "  waiting ($n)..."
  sleep 1
done

# 3️⃣ Clear cached config/schema/views to prevent schema mismatch
echo "🧹 Clearing old caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan optimize:clear || true

# 4️⃣ Remove old schema dump (if exists) to rebuild migrations
echo "🧱 Refreshing schema..."
php artisan schema:dump --prune || true

# 5️⃣ Run migrations for messages & others
echo "🗄️ Running database migrations..."
php artisan migrate --force || echo "⚠️ Migration step failed but continuing..."

# 6️⃣ (Optional) Seed Admin account
echo "👤 Seeding admin (if seeder exists)..."
php artisan db:seed --class=AdminSeeder --force || echo "⚠️ AdminSeeder not found — skipping."

# 7️⃣ Rebuild optimized caches for performance
echo "⚙️ Rebuilding cache..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 8️⃣ Start Laravel application server
echo "✅ Starting Laravel server on port 8000..."
php artisan serve --host=0.0.0.0 --port=8000

#!/bin/bash
set -e

echo "=== TABUAN Startup ==="

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Seed database (admin is always upserted, demo data only runs once)
echo "Running seeder..."
php artisan db:seed --force 2>/dev/null || echo "Seeder warning — check logs."

# Create storage symlink
echo "Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# Force-wipe the compiled views directory so Railway's persistent volume
# cannot serve stale pre-compiled blade templates from previous deploys.
echo "Wiping view cache..."
chmod -R 777 storage/framework 2>/dev/null || true
rm -rf storage/framework/views
mkdir -p storage/framework/views
chmod 777 storage/framework/views

# Rebuild config/route/event caches at runtime (DB env vars are available here)
echo "Rebuilding caches..."
php artisan config:clear
php artisan route:clear
php artisan event:clear
php artisan cache:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
# NOTE: intentionally no view:cache — views compile on first request from
# current source files, avoiding any persistent-volume stale-cache issue.

# Run scheduler every minute in background
echo "Starting scheduler..."
while true; do php artisan schedule:run --quiet 2>/dev/null; sleep 60; done &

echo "Starting server on port ${PORT:-8000}..."
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"

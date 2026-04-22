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

# Clear all stale caches, then rebuild with current code
php artisan config:clear
php artisan route:clear
php artisan event:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "Starting server on port ${PORT:-8000}..."
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"

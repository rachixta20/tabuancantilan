#!/bin/bash
set -e

echo "=== TABUAN Startup ==="

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Seed database only if no admin exists yet
echo "Checking seed status..."
php artisan db:seed --force 2>/dev/null || echo "Seeder skipped (already seeded)."

# Create storage symlink
echo "Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# Cache config/routes/events/views at runtime so DB env vars are available
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "Starting server on port ${PORT:-8000}..."
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"

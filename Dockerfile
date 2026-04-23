
# ── Stage 1: Build frontend assets ──────────────────────────────────────────
FROM node:20-slim AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources/ resources/

RUN npm run build

# ── Stage 2: PHP application ─────────────────────────────────────────────────
FROM php:8.2-cli

# install-php-extensions uses pre-compiled binaries — no source compilation
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions mbstring pdo pdo_mysql pdo_pgsql gd exif pcntl bcmath

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

COPY . .

COPY --from=frontend /app/public/build/ ./public/build/

RUN composer dump-autoload --optimize --no-interaction

RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8000
CMD ["bash", "start.sh"]

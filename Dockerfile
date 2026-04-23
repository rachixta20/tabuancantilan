
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

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip \
    libonig-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd \
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

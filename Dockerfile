
# ── Stage 1: Build frontend assets (Node.js 20, Debian-based = no musl issues) ─
FROM node:20-slim AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources/ resources/

RUN npm run build

# ── Stage 2: PHP application ──────────────────────────────────────────────────
FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    git curl zip unzip bash \
    oniguruma-dev \
    libpng-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

COPY . .

# Always use the freshly compiled assets from the build stage (never stale)
COPY --from=frontend /app/public/build/ ./public/build/

RUN composer dump-autoload --optimize --no-interaction

RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8000
CMD ["bash", "start.sh"]

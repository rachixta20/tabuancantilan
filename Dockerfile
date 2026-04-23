FROM php:8.2-cli-alpine

# System dependencies
RUN apk add --no-cache \
    git curl zip unzip \
    nodejs npm \
    oniguruma-dev \
    libpng-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# ── Dependency layers (cached until lock files change) ──────────────────────
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

COPY package*.json ./
RUN npm ci

# ── Application source (this layer is ALWAYS rebuilt when any file changes) ─
COPY . .

RUN composer dump-autoload --optimize --no-interaction

# Build CSS/JS assets
RUN npm run build

# Storage directories
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8000
CMD ["bash", "start.sh"]

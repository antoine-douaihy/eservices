# ── Build stage: install PHP deps, build front-end assets ──────────────────
FROM php:8.2-cli AS build

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip gd bcmath exif \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Install PHP deps first (cached unless composer.* changes)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Install JS deps (cached unless package.json changes)
COPY package.json package-lock.json* ./
RUN npm ci

# Now copy the rest of the app and finish the build
COPY . .
RUN composer dump-autoload --optimize \
    && npm run build \
    && rm -rf node_modules

# ── Runtime stage: just what's needed to run the app ────────────────────────
FROM php:8.2-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip gd bcmath exif \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY --from=build /app /app

# storage/ and bootstrap/cache/ must be writable
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

# 1. Run migrations on every boot (safe/idempotent — Laravel skips ones
#    already applied), then start the app on whatever port the host gives us.
# 2. Render (and most PaaS hosts) inject $PORT — default to 10000 locally.
CMD php artisan config:cache \
    && php artisan storage:link || true \
    && php artisan migrate --force \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}

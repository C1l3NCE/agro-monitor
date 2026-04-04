# ---------- FRONTEND (VITE BUILD) ----------
FROM node:18 AS node

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# ---------- BACKEND (PHP) ----------
FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем проект
COPY . .

# Копируем собранный фронт
COPY --from=node /app/public/build /var/www/public/build

# Устанавливаем зависимости Laravel
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# Права (иногда важно)
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 10000

CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=10000
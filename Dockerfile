FROM php:8.2-cli

WORKDIR /var/www

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем проект
COPY . .

# PHP зависимости
RUN composer install --no-dev --optimize-autoloader

# Установка и сборка фронта
RUN npm install
RUN npm run build

# Миграции
RUN php artisan key:generate || true

EXPOSE 10000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000

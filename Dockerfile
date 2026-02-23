FROM php:8.2-cli

WORKDIR /var/www

# Системные зависимости
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

# Установка зависимостей
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# Production cache (ВМЕСТО clear)
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

EXPOSE 10000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
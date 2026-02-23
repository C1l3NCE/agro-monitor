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
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем проект
COPY . .

# Устанавливаем зависимости
RUN composer install --no-dev --optimize-autoloader

# Генерируем ключ если нужно
RUN php artisan key:generate || true

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000
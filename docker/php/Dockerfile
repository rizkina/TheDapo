FROM php:8.3-fpm-alpine

# 1. Install dependencies
RUN apk add --no-cache \
    bash curl libpng-dev libzip-dev zlib-dev icu-dev \
    postgresql-dev libpq linux-headers postgresql-client \
    libjpeg-turbo-dev freetype-dev git unzip

# 2. Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql gd zip intl bcmath pcntl opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 3. Copy file konfigurasi composer dulu
COPY composer.json composer.lock ./

# 4. Install vendor (Ini akan masuk ke dalam IMAGE)
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

# 5. Copy seluruh isi project
COPY . .

# 6. Generate autoload & setup folder
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

COPY docker/php/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
EXPOSE 9000
CMD ["php-fpm"]
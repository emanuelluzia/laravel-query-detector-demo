FROM php:8.3-fpm

# Extensões necessárias
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
 && docker-php-ext-install pdo_mysql zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Permissões simples pro storage/bootstrap
RUN mkdir -p storage framework/cache bootstrap/cache \
 && chown -R www-data:www-data /var/www

CMD ["php-fpm"]

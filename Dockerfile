# 1) Build vendor deps
FROM composer:2.6.5 as vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# 2) Runtime: PHP-FPM + Nginx
FROM php:8.2-fpm-alpine AS production

# System deps for building PHP extensions
RUN apk update && apk add --no-cache \
    nginx \
    build-base \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    oniguruma-dev \
    icu-dev \
    postgresql-dev \
    git \
    curl

# PHP extensions
# - mbstring, bcmath: Laravel
# - zip, opcache: common
# - gd: for image handling (jpeg/png)
RUN docker-php-ext-configure gd --with-jpeg --with-freetype=no \
 && docker-php-ext-install pdo pdo_pgsql zip opcache mbstring bcmath gd

# Copy vendor from builder
WORKDIR /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copy app
COPY . /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx config and startup script
COPY docker/nginx.main.conf /etc/nginx/nginx.conf
COPY docker/laravel.conf /etc/nginx/conf.d/laravel.conf
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh && sed -i 's/\r$//' /usr/local/bin/start.sh

EXPOSE 80
CMD ["/usr/local/bin/start.sh"]
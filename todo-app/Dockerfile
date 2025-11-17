# Stage 1: Build Composer dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Build Node assets
FROM node:18 AS nodebuild
WORKDIR /app
COPY . .
RUN npm install && npm run build

# Stage 3: PHP-FPM + Nginx
FROM php:8.2-fpm

# Install required packages
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    nginx \
    supervisor \
    libonig-dev \
    zlib1g-dev \
 && docker-php-ext-install pdo pdo_pgsql mbstring bcmath zip

WORKDIR /var/www/html

# Copy Laravel app from previous build stages
COPY --from=vendor /app/vendor ./vendor
COPY --from=nodebuild /app/public/build ./public/build
COPY . .

# Copy Nginx and Supervisor configs
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

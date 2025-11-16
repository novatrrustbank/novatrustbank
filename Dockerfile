# Dockerfile for Laravel on Render (Postgres)
FROM php:8.2-fpm

ENV DEBIAN_FRONTEND=noninteractive

# Install system packages + PHP extensions (including pdo_pgsql)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libpq-dev libzip-dev \
 && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies (production)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ✅ Create storage symlink for uploads
RUN php artisan storage:link || true

# Copy start script
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

# Ensure storage and cache writable
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose Laravel port
EXPOSE 8000

# Start script handles key generation + waiting for DB + migrate & seed, then serve
CMD ["/start.sh"]

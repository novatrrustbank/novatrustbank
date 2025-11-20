FROM php:8.1-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && 

mv composer.phar /usr/local/bin/composer

WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies (production)
RUN composer install --no-dev --optimize-autoloader --

no-interaction

# Create storage symlink
RUN php artisan storage:link || true

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose Laravel port
EXPOSE 8000

# Start app
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--

port=8000"]

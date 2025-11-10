# Use PHP 8.2 with FPM
FROM php:8.2-fpm

# Install required system packages
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libpq-dev libzip-dev && \
    docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy .env if not already existing (Render uses environment variables)
# Generate Laravel app key
RUN php artisan key:generate --force || true

# Clear and cache configuration
RUN php artisan config:clear && php artisan config:cache || true

# Run migrations safely (ignore if DB not ready)
RUN php artisan migrate --force || true

# Set permissions for storage and cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Start Laravel server
CMD php artisan serve --host=0.0.0.0 --port=8000

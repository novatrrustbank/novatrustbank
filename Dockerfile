# Use official PHP with Apache
FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Install dependencies
RUN apt-get update && apt-get install -y \
    git zip unzip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project
COPY . .

# Install vendors
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Give Apache permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

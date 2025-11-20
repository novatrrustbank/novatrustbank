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

RUN a2enmod rewrite
RUN sed -i 's#/var/www/html#/var/www/public#g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

CMD ["apache2ctl", "-D", "FOREGROUND"]

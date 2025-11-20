FROM php:8.2-apache

# Enable Apache Rewrite

RUN a2enmod rewrite

# Install system utilities

RUN apt-get update && apt-get install -y 
git zip unzip

# Install Composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory

WORKDIR /var/www/html

# Copy application code

COPY . .

# Install PHP dependencies

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set Apache DocumentRoot to public folder

RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess files

RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set proper permissions

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]

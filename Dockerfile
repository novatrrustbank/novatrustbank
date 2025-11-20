FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Update & install packages
RUN apt-get update && apt-get install -y \
    git zip unzip libpq-dev

# Install PHP drivers (Postgres + MySQL)
RUN docker-php-ext-install pdo pdo_pgsql pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project
COPY . .

# Install vendors
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Point Apache to Laravel public folder
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]

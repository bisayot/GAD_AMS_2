# 1. Use the official PHP image with Apache pre-installed
FROM php:8.2-apache

# 2. Install system dependencies and PHP extensions required by CodeIgniter 4
RUN apt-get update && apt-get install -y \
    libicu-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl mysqli pdo pdo_mysql

# 3. Enable Apache mod_rewrite (Crucial for CI4 URL routing)
RUN a2enmod rewrite

# 4. Change Apache document root to the CI4 /public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Set the working directory inside the server
WORKDIR /var/www/html

# 7. Copy ONLY the Composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# 8. Install CodeIgniter dependencies (with memory limit bypassed)
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader

# 9. NOW copy the rest of your backend files into the container
COPY . .

# 10. Set the correct permissions so CI4 can write to its cache and log folders
RUN chown -R www-data:www-data writable/ public/
RUN chmod -R 775 writable/

# 11. Start the Apache web server
CMD ["apache2-foreground"]
# Use official PHP image with Apache
FROM php:8.2-apache

# Copy project files to Apache root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Enable required PHP extensions
RUN docker-php-ext-install mysqli && a2enmod rewrite

# Give permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

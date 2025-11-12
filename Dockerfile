# Use official PHP image with Apache
FROM php:8.2-apache

# Copy all project files to Apache root
COPY . /var/www/html/

# Set working directory to public
WORKDIR /var/www/html/public

# Enable mysqli extension and Apache rewrite
RUN docker-php-ext-install mysqli && a2enmod rewrite

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port
EXPOSE 80

# Run Apache
CMD ["apache2-foreground"]

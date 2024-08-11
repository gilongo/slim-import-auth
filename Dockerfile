# Use the official PHP 8.3 Apache image
FROM php:8.3-apache

# Set the working directory
WORKDIR /var/www/

# Install necessary dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo_pgsql pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/

# Copy the virtual host configuration file
COPY .docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable the new virtual host configuration
RUN a2ensite 000-default.conf

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Apache actions
RUN a2enmod actions

# Expose port 80
EXPOSE 80

# Set the entrypoint to start Apache
CMD ["apache2-foreground"]
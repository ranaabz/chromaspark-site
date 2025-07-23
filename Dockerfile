FROM php:8.1-apache

# Install PostgreSQL extensions
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pgsql pdo_pgsql && \
    apt-get clean

# Enable mod_rewrite if needed
RUN a2enmod rewrite

# Copy source files into the Apache document root
WORKDIR /var/www/html
COPY . .

# Set proper permissions if needed
RUN chown -R www-data:www-data /var/www/html

# Expose the container’s HTTP port
EXPOSE 80
